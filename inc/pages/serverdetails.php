<?php
echo "<h1>$page</h1>\n";

$dblink = db_connect();

$server = mysqli_real_escape_string($dblink, $page);

$dbquery = "SELECT * FROM systems WHERE hostname='$server';";
$dbresult = mysqli_query($dblink, $dbquery);
if (!$dbresult || mysqli_num_rows($dbresult) != 1) {
	echo "ERROR: Not found\n";
	include 'inc/stdfoot.php';
	exit;
}
$row = mysqli_fetch_object($dbresult);

?>
<div class='contcont'>
        <div class='contentbox'>
                <h2>System Info</h2>
		<div class='content'>
<?php
	echo "<table>\n";
	echo "<tr><th>Server Name</th><td>$row->hostname</td></tr>\n";
	echo "<tr><th>IP Address</th><td>$row->ipaddr</td></tr>\n";
	if ($row->distro == 'ubuntu') $displaydistro = "Ubuntu"; else $displaydistro = $row->distro;
	echo "<tr><th>Distro</th><td>$displaydistro $row->distrorelease</td></tr>\n";
	echo "<tr><th>Reboot Required?</th><td>$row->rebootreq</td></tr>\n";
	echo "<tr><th>Update Requested?</th><td>$row->pushupdates</td></tr>\n";
	$updateactive = $row->pushupdates;
	echo "<tr><th>Last Checkin</th><td>$row->checkin</td></tr>\n";
	echo "</table>\n";
?>

		</div>
	</div>
	<div class='contentbox'>
		<h2>Update Summary</h2>
		<div class='content'>
<?php
	$dbquery = "SELECT COUNT(*) AS cnt, SUM(IF (packtype='bug',1,0)) AS bug, SUM(IF (packtype='sec',1,0)) AS sec FROM packages WHERE hostname='$server' GROUP BY hostname;";
	$dbresult = mysqli_query($dblink, $dbquery);
	if (mysqli_num_rows($dbresult) == 0) {
		echo "<h3 class='goodinv' style='padding:2em;'>No updates, 100% patched!</h3>\n";
	} else {
		list($updatecnt, $bugcnt, $seccnt) = mysqli_fetch_row($dbresult);
		echo "<h3 style='text-align:center;'>$updatecnt updates available</h3>\n";
		echo "<table>\n";
		//	echo "<tr><th>Security Updates</th><td>$seccnt</td></tr>\n";
		echo "<tr><th>Security Updates</th><th>Non-Security Updates</th></tr>\n";
		echo "<tr><td style='text-align:center;font-size:2em;' class='badinv'>$seccnt</td><td style='text-align:center;font-size:2em;' class='warninginv'>$bugcnt</td></tr>\n";
		//	echo "<tr><th>Non-Security Updates</th><td>$bugcnt</td></tr>\n";
		echo "</table>\n";
	}
?>
		</div>
	</div>
	<div class='contentbox'>
		<h2>Server Actions</h2>
		<div class='content'>
			<table>
<?php
	echo "<tr>";
	echo "<td><a class='red button' href='/actions_add_remove.php?page=serverdetails&action=delserver&servername=$row->hostname'>Delete</a></td>";
	if( $updateactive == 'yes'){
		echo "<td><a class='red button' href='/actions_add_remove.php?page=serverdetails&action=cancelupdateserver&servername=$row->hostname'>Marked Update</a></td>";
	} else{
		echo "<td><a class='red button' href='/actions_add_remove.php?page=serverdetails&action=updateserver&servername=$row->hostname'>Update</a></td>";
	}
	echo "</tr>";
	echo "<tr>";
	echo "<td><a target='_blank' class='red button' href='/view_page.php?action=viewallpkgs&servername=$row->hostname'>View All Pkgs</a></td>";
	echo "<td><a target='_blank' class='red button' href='/view_page.php?action=viewsecerrata&servername=$row->hostname'>View Sec Errata</a></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td><a target='_blank' class='red button' href='/view_page.php?action=transactionlog&servername=$row->hostname'>View Trans Log</a></td>";
	echo "<td><a class='red button' href='/actions_add_remove.php?page=serverdetails&action=resetupdatestatus&servername=$row->hostname'>Reset Update Status</a></td></tr>\n";
	echo "</tr>";
?>
			</table>
		</div>
	</div>
	<div class='wide contentbox'>
		<h2>Update Details</h2>
		<div class='content'>
<?php
	$dbquery = "SELECT p.packagename, p.oldpackageversion, p.newpackageversion, p.shortdesc, p.packtype, e.usn, e.dateusn FROM packages AS p LEFT JOIN ubparser AS e ON p.packagename=e.packagename AND p.newpackageversion=e.packagever WHERE hostname='$server' ORDER BY packtype DESC, packagename ASC;";
//	$dbquery = "SELECT * FROM packages WHERE hostname='$server' ORDER BY packtype DESC, packagename ASC;";
	$dbresult = mysqli_query($dblink, $dbquery);
	if (mysqli_num_rows($dbresult) == 0) {
		echo "<h3>No updates, 100% patched!</h3>\n";
	} else {
		echo "<table>\n";
		echo "<tr><th>Package</th><th>Installed Ver</th><th>Updated Ver</th><th>Type</th><th>Description</th><th>Errata</th></tr>\n";
		while ($row = mysqli_fetch_object($dbresult)) {
			if ($row->packtype == 'bug') $icon = "<i class='fa fa-bug warning'></i>";
			if ($row->packtype == 'sec') $icon = "<i class='fa fa-shield bad'></i>";
			if ($row->usn == '') $erratadisp = "N/A"; else $erratadisp = "<a href='https://www.ubuntu.com/usn/$row->usn/' target='_blank'>$row->usn ($row->dateusn)</a>";
			echo "<tr><td>$row->packagename</td><td>$row->oldpackageversion</td><td>$row->newpackageversion</td><td>$icon</td><td>$row->shortdesc</td><td>$erratadisp</td></tr>\n";
		}
		echo "</table>\n";
	}
	
?>
		</div>
	</div>
</div>
