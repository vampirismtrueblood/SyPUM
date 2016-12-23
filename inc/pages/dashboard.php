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





echo "<h1>My Dashboard</h1>";

echo "<div class='contentbox'>\n";
echo "<h2>List Of Connected Servers</h2>";
echo "<div class='content'>\n";

		$dblink = db_connect();
		$dbquery = "SELECT * FROM updates;";
		$dbresult = mysqli_query($dblink, $dbquery);
echo "<table>\n";
		echo "<tr><td>Server Name</td><td>IP Addr</td><td>Packages</td><td>Security Packages</td><td>Release</td><td>Last Checkin</td><td>Updated</td></tr><br/>\n";
		if ($dbresult && mysqli_num_rows($dbresult)) {
			while ($row = mysqli_fetch_object($dbresult)) {
				$dbquery2 = "select * from systemssecurity where hostname='$row->hostname'";
				$dbresult2 = mysqli_query($dblink, $dbquery2);
				$dbquery3 = " select * from systems where hostname='$row->hostname'";
				$dbresult3 = mysqli_query($dblink, $dbquery3);
				
			       echo "<tr><td>$row->hostname</td><td>$row->ipaddr</td><td>" .  mysqli_num_rows($dbresult3) . "</td><td>" .  mysqli_num_rows($dbresult2) . "</td><td>$row->ubunturelease</td><td>$row->checkin</td><td>$row->updated</td><td>";
        echo "<a class='red button' href='actions_add_remove.php?action=delserver&servername=$row->hostname&ubureleasever=$row->ubunturelease'>Delete</a>";
	$dbquery4 = "select pushupdates from updates where hostname='$row->hostname'";
	$dbresult4 = mysqli_query($dblink, $dbquery4);
	$temp1=mysqli_fetch_row($dbresult4);
	$updateactive=$temp1[0];
	if( $updateactive == 'yes'){
        echo "</td><td><a class='red button' href='actions_add_remove.php?action=cancelupdateserver&servername=$row->hostname&ubureleasever=$row->ubunturelease'>Marked Update</a>";
	} else{
	echo "</td><td><a class='red button' href='actions_add_remove.php?action=updateserver&servername=$row->hostname&ubureleasever=$row->ubunturelease'>Update</a>";
}
        echo "</td>";


	echo "<td><a class='red button' href='view_page.php?action=viewallpkgs&servername=$row->hostname&ubureleasever=$row->ubunturelease'>View All Pkgs</a></td>";
	echo "<td><a class='red button' href='view_page.php?action=viewsecerrata&servername=$row->hostname&ubureleasever=$row->ubunturelease'>View Sec Errata</a></td>";
	echo "<td><a class='red button' href='view_page.php?action=transactionlog&servername=$row->hostname&ubureleasever=$row->ubunturelease'>View Trans Log</a></td>";
	echo "<td><a class='red button' href='actions_add_remove.php?action=resetupdatestatus&servername=$row->hostname&ubureleasever=$row->ubunturelease'>Reset Update Status</a></td></tr>\n";


}	
	} else {
			echo "No servers found... You could add one...\n";
		}

echo"</table>";
echo "</div>\n";
echo "</div>\n";
?>

