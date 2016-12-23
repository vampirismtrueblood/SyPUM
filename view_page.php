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




require_once 'inc/stdhead.php';


require_once('inc/db.php');

$dblink=db_connect();

$servername=mysqli_real_escape_string($dblink,$_GET['servername']);
$Action=mysqli_real_escape_string($dblink,$_GET['action']);
$Ubureleasever=mysqli_real_escape_string($dblink,$_GET['ubureleasever']);

//echo "<h1>Host $servername</h1>";
if($Action == 'viewallpkgs'){
echo "<div class='contentbox'>\n";
echo "<h2>List of Pkgs to be updated on $servername</h2>";
echo "<div class='content'>\n";
                $dbquery = "SELECT packagename FROM systems where hostname='$servername';";
                $dbresult = mysqli_query($dblink, $dbquery);
echo "<table>\n";
                echo "<tr><td>Package</td><td>Is Sec pkg?</td><td>Short Sec Desc</td></tr><br/>\n";
                if ($dbresult && mysqli_num_rows($dbresult)) {
                        while ($row = mysqli_fetch_object($dbresult)) {
				 $dbquery2 = "select packagename from systemssecurity where hostname='$servername' and packagename='$row->packagename'";
                                $dbresult2 = mysqli_query($dblink, $dbquery2);
				if ($dbresult && mysqli_num_rows($dbresult2)) $security='yes'; else $security='no';
	if($security == 'yes'){
	 echo "<tr><td>$row->packagename</td><td>Yes</td></tr>\n";
}else{
	echo "<tr><td>$row->packagename</td><td>No</td></tr>\n";
}


}
}
else {
                        echo "No servers found... You could add one...\n";
                }

echo"</table>";
echo "</div>\n";
echo "</div>\n";
}

if($Action == 'viewsecerrata'){
echo "<div class='contentbox'>\n";
echo "<h2>List of Errata for host $servername</h2>";
echo "<div class='content'>\n";
$uburel_tmp=explode('.',$Ubureleasever);
$uburel=$uburel_tmp[0];
                $dbquery = "select ss.newpackageversion,ss.oldpackageversion,ubp.details,ubp.swdesc,ubp.packagename,ubp.packagever from ubparser as ubp LEFT JOIN systemssecurity as ss ON ss.packagename=ubp.packagename where ss.hostname='$servername' and ubp.releasever like 'Ubuntu $Ubureleasever%' and ubp.packagever = ss.newpackageversion;";
                $dbresult = mysqli_query($dblink, $dbquery);
echo "<table>\n";
                echo "<tr><td>Package</td><td>Curr Ver</td><td>latest Ver</td><td>Short Desc</td><td>Details</td></tr><br/>\n";
                if ($dbresult && mysqli_num_rows($dbresult)) {
                        while ($row = mysqli_fetch_object($dbresult)) {
                  echo "<tr><td>$row->packagename</td><td>$row->oldpackageversion</td><td>$row->packagever</td><td>$row->swdesc</td><td>$row->details</td></tr>\n";
//		echo "<tr><td>$row->packagename</td><td>$row->oldpackageversion</td><td>$row->newpackageversion</td><td>$row->swdesc</td><td>$row->details</td></tr>\n";
}
}
else {
                        echo "No servers found... You could add one...\n";
                }

echo"</table>";
echo "</div>\n";
echo "</div>\n";
}

if($Action == 'transactionlog'){
echo "<div class='contentbox'>\n";
echo "<h2>Transaction Logs for host $servername</h2>";
echo "<div class='content'>\n";
                $dbquery = "SELECT datelog FROM systemstransactionlogs where hostname='$servername'";
                $dbresult = mysqli_query($dblink, $dbquery);
echo "<table>\n";
                echo "<tr><td>Transaction Date/time</td><td>View</td></tr><br/>\n";
                if ($dbresult && mysqli_num_rows($dbresult)) {
                        while ($row = mysqli_fetch_object($dbresult)) {
                  echo "<tr><td>$row->datelog</td>";
echo "<td><a class='red button' href='view_page.php?action=viewtranslogdate&servername=$servername&transdate=$row->datelog'>View Trans Log</a></td></tr>\n";
}
}
else {
                        echo "No servers found... You could add one...\n";
                }

echo"</table>";
echo "</div>\n";
echo "</div>\n";
}

if($Action == 'viewtranslogdate'){
if(isset($_GET['transdate'])) $transDate=$_GET['transdate'];
echo "<div class='contentboxpeter'>\n";
echo "<h2>Logs for $transDate on host $servername</h2>";
echo "<div class='content'>\n";
                $dbquery = "SELECT transactionlog FROM systemstransactionlogs where hostname='$servername' and datelog='$transDate'";
                $dbresult = mysqli_query($dblink, $dbquery);
		if(!$dbresult) echo "Error ..." . mysqli_error($dblink) . "\n";
echo "<table>\n";
                echo "<tr><td>Log $transDate</td></tr><br/>\n";
                if ($dbresult && mysqli_num_rows($dbresult)) {
                        while ($row = mysqli_fetch_object($dbresult)) {
//                  echo "<tr><td>$row->transactionlog</td></tr>\n";
		echo $row->transactionlog . "\n";
}
}
else {
                        echo "No servers found... You could add one...\n";
                }

echo"</table>";
echo "</div>\n";
echo "</div>\n";
}



require_once 'inc/stdfoot.php';

?>
