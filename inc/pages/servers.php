<?php

// Project SyPUM - Systems Package Update Management 
//
// dashboard.php
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

if(isset($_GET['msg'])) echo $_GET['msg'];

if (substr($section, -3) == 'csv') {
	$csv = TRUE;
} else {
	$csv = FALSE;
}

switch ($page) {
	case 'withbugsandupdates':
		$title = "List of Servers with Bugs and Sec Updates";
		$serversqlcond = "WHERE hostname in (SELECT distinct hostname from packages where packtype='sec')";
		break;
	
	case 'withbugs':
		$title = "List of Servers with Bugs";
		$serversqlcond = "WHERE hostname not in (SELECT distinct hostname from packages where packtype='sec')";
		break;

	case 'offline':
		$title = "List of offline Servers";
		$serversqlcond = "WHERE checkin <= now() - INTERVAL 1 DAY";
		break;

	case 'fullyupdated':
		$title = "List of Servers Fully Updated";
		$serversqlcond = "WHERE hostname not in (SELECT distinct hostname from packages)";
		break;

	case 'isbanned':
		$title = "List of Banned(Deleted) Servers";
		$serversqlcond = "WHERE isbanned='YES'";
		break;

	case '':
		$title = "List Of Connected Servers";
		$serversqlcond = "";
		break;

	default:
		require 'serverdetails.php';
		require 'inc/stdfoot.php';
		exit();
		break;
}

if (!$csv) {
	echo "<h1>Servers</h1>";

	echo "<div class='contentbox'>\n";
	echo "<h2>$title</h2>";
	echo "<div class='content'>\n";
} else {
	$csvh = fopen('php://memory', 'w');
}

$dblink = db_connect();
$dbquery = "SELECT * FROM systems $serversqlcond;";
$dbresult = mysqli_query($dblink, $dbquery);
if (!$csv) {
	echo "<table>\n";
	echo "<tr><th>Server Name</th><th>IP Addr</th><th>Updates</th><th>Release</th><th>Last Checkin</th><th>Updated</th><th>Reboot Required</th><th colspan=6>Actions</th></tr><br/>\n";
} else {
	$csvrow = array('Server Name','IP Addr','Bug Updates','Security Updates','Total Updates','Release','Last Checkin','Reboot Required');
	fputcsv($csvh, $csvrow);
}
if ($dbresult && mysqli_num_rows($dbresult)) {
	while ($row = mysqli_fetch_object($dbresult)) {
		$dbquery2 = "SELECT type, COUNT(DISTINCT packagename) AS cnt FROM (SELECT 'SECURITY' AS type, packagename FROM packages WHERE hostname='$row->hostname' and packtype='sec' UNION SELECT 'UPDATE' as type, packagename FROM packages WHERE hostname='$row->hostname' and packtype='bug') AS allupdates GROUP BY type WITH ROLLUP;";
		$dbresult2 = mysqli_query($dblink, $dbquery2);
		$updatecounts = array('UPDATE' => 0, 'SECURITY' => 0, 'TOTAL' => 0);
		while ($row2 = mysqli_fetch_object($dbresult2)) {
			if ($row2->type=='') $type = 'TOTAL'; else $type = $row2->type;
			$updatecounts[$type] = $row2->cnt;
		}
		if ($csv) {
			$csvrow = array($row->hostname,$row->ipaddr,$updatecounts['UPDATE'],$updatecounts['SECURITY'],$updatecounts['TOTAL'],$row->distrorelease,$row->checkin,$row->rebootreq);
			fputcsv($csvh, $csvrow);
		} else {
			echo "<tr><td><a href='/servers/$row->hostname'>$row->hostname</a></td><td>$row->ipaddr</td>";
			echo "<td><table><tr><td colspan=2 style='text-align: center;'>{$updatecounts['TOTAL']}</td></tr>";
			echo "<tr>";
			echo "<td width='50%' class='warning'><i class='fa fa-bug'></i>&nbsp;{$updatecounts['UPDATE']}</td>";
			echo "<td width='50%' class='bad'><i class='fa fa-shield'></i>&nbsp;{$updatecounts['SECURITY']}</td>";

			echo "</tr>";
			echo "</table></td>";
			if($row->updated == 'yes') {
				echo "<td>$row->distrorelease</td><td>$row->checkin</td><td width='2%'><i class='fa fa-check good' aria-hidden='true'></td>";
			}else{
				echo "<td>$row->distrorelease</td><td>$row->checkin</td><td width='2%'><i class='fa fa-times bad' aria-hidden='true'></td>";
			}
		

			if($page != 'offline'){	
			if($row->rebootreq == 'YES') {
                                echo "<td width='2%'><i class='fa fa-wrench bad' aria-hidden='true'></td><td>";
                        }else{
                        	echo "<td width='2%'><i class='fa fa-cogs good' aria-hidden='true'></td><td>";
			}
			}else { 

echo "<td style='text-align: center;' width='2%'><i class='fa fa-question-circle-o fa-spin bad fa-2x' aria-hidden='true'></td><td>"; 
//	echo '<td style="text-align: center;"> <span class="fa-stack fa-lg"> <i class="fa fa-coffee fa-stack-1x"></i>   <i class="fa fa-ban fa-stack-2x bad"></i></span> </td>';
	
	}

			echo "<a class='red button' href='/actions_add_remove.php?action=delserver&servername=$row->hostname&ubureleasever=$row->distrorelease'>Delete</a>";
			$dbquery4 = "select pushupdates from systems where hostname='$row->hostname'";
			$dbresult4 = mysqli_query($dblink, $dbquery4);
			$temp1=mysqli_fetch_row($dbresult4);
			$updateactive=$temp1[0];
			if( $updateactive == 'yes'){
				echo "</td><td><a class='red button' href='/actions_add_remove.php?action=cancelupdateserver&servername=$row->hostname&ubureleasever=$row->distrorelease&mypage=$page'>Marked Update</a>";
			} else{
				echo "</td><td><a class='red button' href='/actions_add_remove.php?action=updateserver&servername=$row->hostname&ubureleasever=$row->distrorelease&mypage=$page'>Update</a>";
			}
			echo "</td>";


			echo "<td><a class='red button' href='/view_page.php?action=viewallpkgs&servername=$row->hostname&ubureleasever=$row->distrorelease&mypage=$page'>View All Pkgs</a></td>";
			echo "<td><a class='red button' href='/view_page.php?action=viewsecerrata&servername=$row->hostname&ubureleasever=$row->distrorelease&mypage=$page'>View Sec Errata</a></td>";
			echo "<td><a class='red button' href='/view_page.php?action=transactionlog&servername=$row->hostname&ubureleasever=$row->distrorelease&mypage=$page'>View Trans Log</a></td>";
			echo "<td><a class='red button' href='/actions_add_remove.php?action=resetupdatestatus&servername=$row->hostname&ubureleasever=$row->distrorelease&mypage=$page'>Reset Update Status</a></td></tr>\n";
		}
	}	
} else {
	echo "No servers found... \n";
}
if (!$csv) {
	echo "</table>\n";
	echo "<a class='sig1 button' href='/{$section}csv/$page'>Get as CSV</a>\n";
	echo "</div>\n";
	echo "</div>\n";
} else {
	fseek($csvh, 0);
	$csv = stream_get_contents($csvh);
	/* sending to browser - download */
	if ($page == '') $filepage = 'all'; else $filepage = $page;
	$filedatetime = date('Ymd-His');
	header('Content-Type: application/csv');
	header("Content-Disposition: attachement; filename='servers-$filepage-$filedatetime.csv';");
	echo $csv;
	exit;
}
?>

