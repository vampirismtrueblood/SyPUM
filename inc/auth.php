<?php
// Project SyPUM - Systems Package Update Management
//
//
// Author: Dan Tucny - 12/20/2016 All Rights Reserved
//
// Copyright 2016 Dan Tucny - d@tucny.com
// This file is part of MAN Spider is distributed under the terms of the GNU General Public License
/*
    SyPUM - Systems Package Update Management is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    SyPUM - Systems Package Update Management is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

function ldapping($host, $port=389, $timeout=1) {
        $fsh = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$fsh) {
                return FALSE;
        } else {
                fclose($fsh); //explicitly close open socket connection
                return TRUE; //DC is up & running, we can safely connect with ldap_connect
        }
}

function ldapconnect() {
	// We probably need TLS with any reasonably secure system.
	// If working with a very insecure system, set $requiretls to FALSE, if you don't even want to try TLS, set $attempttls to FALSE
	// If you need to force speaking ldaps on port 636, even though SRV records don't offer it, set $forceldaps to TRUE;
	$attempttls = TRUE;
	$requiretls = TRUE;
	$forceldaps = FALSE;

	// Domain can be forced here in the event gethostname doesn't include the domain or you need to force a different domain
	//global $domain;
	//$domain = 'acqict.xyz';

	// Get our hostname and domain name
	$hostname = gethostname();
	if (!isset($domain)) {
		global $domain;
		$domain = substr($hostname, strpos($hostname, '.') + 1);
	}

	// Get the LDAP servers for our domain, shuffle them and sort by priority. *TODO* - sorting by proximity would be nice
	$ldapservers = dns_get_record('_ldap._tcp.' . $domain, DNS_SRV);
	$sortPrioWeight = function($a, $b) {
		if ($a['pri'] != $b['pri']) {
			return $b['pri'] - $a['pri'];
		}
		return $a['weight'] - $b['weight'];
	};
//	echo "$sortPrioWeight\n";
	shuffle($ldapservers);
	usort($ldapservers, $sortPrioWeight);

	// Check if we are dealing with MSAD
	$msdcs = dns_get_record('_msdcs.' . $domain, DNS_SOA);
	global $msad;
	if (!$msdcs || count($msdcs) == 0) {
		// Either we have a DNS problem, or we're not looking at an MSAD domain
		$msad = FALSE;
	} else {
		$msad = TRUE;
	}

	// Initiate connection to LDAP
	$connected = FALSE;
	foreach ($ldapservers AS $ldapserver) {
		$startts = microtime(TRUE);
		if ($forceldaps) {
			$port = 636;
		} else {
			$port = $ldapserver['port'];
		}
		if ($port == 636) {
			$proto = 'ldaps';
		} else {
			$proto = 'ldap';
		}
		// Checking reachability of this LDAP server
		if (!ldapping($ldapserver['target'], $port)) {
			$endts = microtime(TRUE);
			$dur = $endts - $startts;
			continue;
		}
		$endts = microtime(TRUE);
		$ldapresponsespeed = $endts - $startts;

		// Connecting to LDAP, binding anonymously and getting RootDSE		
		$startts = microtime(TRUE);
		$conn = ldap_connect($proto . '://' . $ldapserver['target'] . ':' . $port);
		if ($conn){
			ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			if ($msad) ldap_set_option($conn, LDAP_OPT_REFERRALS, FALSE);
			if ($attempttls && $proto != 'ldaps') {
				// Enabling TLS on connection
				if (!@ldap_start_tls($conn)) {
					// Couldn't enable TLS
					if ($requiretls) {
						// unable to enable TLS using StartTLS but we require it - attempting failover
						continue;
					}
				}
			}
			if (!@ldap_bind($conn)) {
				$endts = microtime(TRUE);
				$dur = $endts - $startts;
				// Couldn't bind - attempting failover
				continue;
			}
			$res = @ldap_read($conn, '', 'objectclass=*');
			$results = @ldap_get_entries($conn, $res);
			$endts = microtime(TRUE);
			$dur = $endts - $startts;
			global $basedn;
			$basedn = $results[0]['defaultnamingcontext'][0];
			$connected = TRUE;
			return $conn;
		}
	}
	return FALSE;
}

if (!isset($_SESSION['loggedin']) && isset($_POST['user']) && isset($_POST['pass'])) {
	$conn = ldapconnect();
	if ($conn === FALSE) {
		echo "LDAP Failure<br />\n";
		require 'inc/stdfoot.php';
		exit(0);
	}
	$auth_user = $_POST['user'];
	$auth_pass = $_POST['pass'];

		
	// Attempting to rebind as user
	if ($msad) {
		$userdn = trim($auth_user) . '@' . $domain;
	} else {
		$userdn = 'uid=' . trim($auth_user) . ',cn=users,cn=accounts,' . $basedn;
		$groupsuffix = 'cn=groups,cn=accounts,' . $basedn;
	}

	if (!@ldap_bind($conn, $userdn, $auth_pass)) {
		echo "Login failure - please try again<br />\n";
	} else {
		$sr = ldap_read($conn, $userdn, "(objectclass=*)");
		$entry = ldap_first_entry($conn, $sr);
		$attrs = ldap_get_attributes($conn, $entry);

		// Check if password has expired
		$now = date_create('now', timezone_open('Asia/Manila'));

		$pwdexpiry = date_create($attrs['krbPasswordExpiration'][0]);
		date_timezone_set($pwdexpiry, timezone_open('Asia/Manila'));
		$pwdset = date_create($attrs['krbLastPwdChange'][0]);;

		$expiryinterval = date_diff($now, $pwdexpiry);
		$daystoexpiry = date_interval_format($expiryinterval, '%R%a');
		if ($pwdexpiry < $now) {
			if ($pwdset == $pwdexpiry) {
				$expmessage = "Password Administratively Reset: " . date_format($pwdexpiry, 'Y-m-d H:i:s O') . " - " . date_interval_format($expiryinterval, '%ad %hh %im %ss') . " ago - change required";
			} else {
				$expmessage = "Password Expired: " . date_format($pwdexpiry, 'Y-m-d H:i:s O') . " - " . date_interval_format($expiryinterval, '%ad %hh %im %ss') . " ago - change required";
			}
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['displayname'] = $displayname;
			$_SESSION['user'] = $auth_user;
			$_SESSION['auth_user'] = $auth_user;
			$_SESSION['expired'] = TRUE;
		} else {
			if (in_array('cn=sypum,' . $groupsuffix, $attrs['memberOf'])) {
				if (isset($attrs['displayName'][0])) $displayname = $attrs['displayName'][0]; else $displayname = '';
				if (isset($attrs['mail'][0])) $mail = $attrs['mail'][0]; else $mail = '';
				$_SESSION['loggedin'] = TRUE;
				$_SESSION['displayname'] = $displayname;
				$_SESSION['user'] = $auth_user;
				$_SESSION['auth_user'] = $auth_user;
				$_SESSION['expired'] = FALSE;
			} else {
				echo "Access denied<br />\n";
			}
		}
	}
}
if ((isset($_SESSION['loggedin']) && isset($_POST['action']) && $_POST['action'] == 'logoff') || ($_SESSION['expired'] == TRUE && isset($_POST['cancelpwchange']))) {
	$_SESSION = array();
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
	if ($_POST['action'] == 'logoff') {
		$errormsg = 'Logged out.';
	} else {
		$errormsg = 'Password reset cancelled.';
	}
}

if (!isset($_SESSION['loggedin']) && (!isset($_SESSION['auth_user']) || !isset($_POST['pass']))) {
	echo "\t\t</header>\n";
        echo "<div style='clear:both;'></div>\n";
	echo "<div class='contcont'>\n";
        echo "<div class='contentbox'>\n";
        echo "<h2>Authentication Required</h2>\n";
        echo "<div class='content'>\n";
        //echo "You need to login to view this page<br><br>\n";
        echo "<form method='post' name='loginform'>\n";
        echo "<label for='user'>User:</label><input type='text' name='user' autofocus><br>\n";
        echo "<label for='password'>Password:</label><input type='password' name='pass'><br>\n";
        echo "<button class='sig1 button' type='submit' name='action' value='login'>Login</button>\n";
        //echo "<button class='sig1 button' type='submit' name='action' value='register'>Register</button><br>\n";
	echo "<br>\n";
        echo "</form>\n";
	if (isset($errormsg) && $errormsg != '') echo $errormsg . "<br>\n";
        echo "</div></div></div>";
        require 'inc/stdfoot.php';
        exit(0);
}

$displayname = $_SESSION['displayname'];
$user = $_SESSION['auth_user'];

if(isset($_POST['save']) && isset($_SESSION['loggedin']) && $_SESSION['expired'] == TRUE) {
	$auth_user = $_SESSION['auth_user'];
	$auth_pass = trim($_POST['oldpass']);
	$newpass = trim($_POST['newpass']);
	$confirmpass = trim($_POST['confirmpass']);
	$errormsg = "";
	if(!empty($newpass) && !empty($confirmpass)) {
		if($newpass != $confirmpass) {
			$errormsg = "<script>alert('Passwords do not match.');</script>";
		}
		else {
			$conn = ldapconnect();
			// Attempting to rebind as user
			if ($msad) {
				$userdn = trim($auth_user) . '@' . $domain;
			} else {
				$userdn = 'uid=' . trim($auth_user) . ',cn=users,cn=accounts,' . $basedn;
				$groupsuffix = 'cn=groups,cn=accounts,' . $basedn;
			}

			if (!@ldap_bind($conn, $userdn, $auth_pass)) {
				echo "Login failure - please try again<br />\n";
			} else {
				$entry = array();
				$entry["userPassword"] = $newpass;
				if (@ldap_modify($conn, $userdn, $entry) === false) {
					echo "Password Change Failed<br />\n";
				} else {
					echo "Password updated.<br />\n";
					$_SESSION['expired'] = FALSE;
				}
			}
		}
	}
}

if(isset($_SESSION['loggedin']) && $_SESSION['expired'] == TRUE) {
	echo "<div style='clear:both;'></div>\n";
	echo "<div class='contcont'>\n";
	echo "<div class='contentbox'>\n";
	echo "<h2>Set New Password</h2>\n";
	echo "<div class='content'>\n";
	echo $errormsg;
	echo "You need to set a new password (minimum of 8 characters)<br>\n";
	echo "<form method='post' name='loginform'>\n";
	if (isset($auth_pass) && $auth_pass != '') {
		$newpassfocus = ' autofocus';
		$oldpassfocus = '';
	} else {
		$oldpassfocus = ' autofocus';
		$newpassfocus = '';
	}
        echo "<label for='oldpass'>Old Password:</label><input type='password' name='oldpass' value='$auth_pass'$oldpassfocus><br>\n";
        echo "<label for='newpass'>New Password:</label><input type='password' name='newpass'$newpassfocus><br>\n";
        echo "<label for='confirmpass'>Confirm Password:</label><input type='password' name='confirmpass'><br>\n";
	echo "<button class='sig1 button' type='submit' name='save'>Save</button>\n";
	echo "<button class='red button' type='submit' name='cancelpwchange'>Cancel</button><br />\n";
	echo "</form>\n";
	echo "</div></div></div>";
	require 'inc/stdfoot.php';
	exit(0);
}
if (substr($section, -3) != 'csv') {
	echo "<div class='status'>\n";
	echo "\t<h2>Info &amp; Status</h2>\n";
	echo "\t<p>You are logged in as $displayname (" . $_SESSION['user'] . ")</p>\n";
	if ($_SESSION['admin']) echo "\t<p>You have admin level access to this application</p>\n";
	echo "\t<form method='post' name='logoutform' style='display: inline-block'><button class='sig1 button' type='submit' name='action' value='logoff'>Logout?</button></form><a class='sig1 button' href='" . $_SERVER['PHP_SELF'] . "'>Refresh?</a><br />\n";
	echo "</div>\n";
}

