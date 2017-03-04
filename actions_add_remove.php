<?php
// Project SyPUM - Systems Package Update Management
//
//
// Author: Peter Malaty - 12/20/2016 All Rights Reserved
//
// Copyright 2016 Peter G.F Malaty - p@pmalaty.com
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
session_start();
session_cache_limiter('nocache');
$cache_limiter = session_cache_limiter();

if (!isset($_SESSION['loggedin'])) exit();

require_once ('inc/db.php');

function Redirect_Servers ($server, $permanent = false) {
        header('Location: /servers/' . $server, true, $permanent ? 301 : 302);
        exit();
}

$dblink = db_connect();
$action='';
$page=$_GET['mypage'];
if (isset($_GET['action']))  $action=mysqli_real_escape_string($dblink,$_GET['action']);
if (isset($_GET['servername'])) $servername=mysqli_real_escape_string($dblink,$_GET['servername']);

switch ($action) {
	case 'delserver':
		$msg="The server $servername is now deleted, IF CLIENT IS STILL ACTIVE IT WILL COME BACK ON AGAIN FOR SECURITY!!!!\n";
		$query = "DELETE FROM systems WHERE systems.hostname='$servername';";
		$dbres = mysqli_query($dblink, $query);

		Redirect_dashboard("?msg=$msg",false);
		break;
	case 'updateserver':
		$query = "UPDATE systems set pushupdates='yes' where hostname='$servername'";
		$dbres = mysqli_query($dblink, $query);
		break;
	case 'cancelupdateserver':
		$query = "UPDATE systems set pushupdates='no' where hostname='$servername'";
		$dbres = mysqli_query($dblink, $query);
		break;
	case 'resetupdatestatus':
		$query = "UPDATE systems set updated='no' where hostname='$servername'";
		$dbres = mysqli_query($dblink, $query);
		break
	default:
		
}

if (isset($_GET['page']) && $_GET['page'] == 'serverdetails') {
	Redirect_dashboard("$servername",false);
} else {
	Redirect_dashboard("",false);
}


