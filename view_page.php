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
                $dbquery = "SELECT packagename,oldpackageversion,newpackageversion,shortdesc FROM packages where hostname='$servername' AND packtype='bug';";
                $dbresult = mysqli_query($dblink, $dbquery);
		$dbquery2 = "select packagename,oldpackageversion,newpackageversion,shortdesc from packages where hostname='$servername' AND packtype='sec';";
                $dbresult2 = mysqli_query($dblink, $dbquery2);
		 $tmparr1=array();

                                        while ($row2 = mysqli_fetch_object($dbresult)) {
						$entry=$row2->packagename . "@@@" . $row2->oldpackageversion . "@@@" . $row2->newpackageversion . "@@@" . $row2->shortdesc;
                                                if(!in_array($entry,$tmparr1)) $tmparr1[]=$entry; else continue;
                                        }
                                        while ($row2 = mysqli_fetch_object($dbresult2)) {
						$entry=$row2->packagename . "@@@" . $row2->oldpackageversion . "@@@" . $row2->newpackageversion . "@@@" . $row2->shortdesc;
                                                if(!in_array($entry,$tmparr1)) $tmparr1[]=$entry; else continue;
                                        }
sort($tmparr1);
echo "<table>\n";
                echo "<tr><td>Package</td><td>Curr Ver</td><td>Latest Ver</td><td>Is Sec pkg?</td><td>Short Desc</td></tr><br/>\n";
                if (($dbresult && mysqli_num_rows($dbresult)) || ($dbresult2 &&  mysqli_num_rows($dbresult2))) {
                        foreach($tmparr1 as $apackage){
				$tmp999=explode('@@@',$apackage);
				$Packname=$tmp999[0];
				$Packvername=$tmp999[1];
				$NewPackvername=$tmp999[2];
				$Shortdesc=$tmp999[3];
					
				 $dbquery9 = "select packagename from packages where hostname='$servername' and packagename='$Packname' and packtype='sec'";
                                $dbresult9 = mysqli_query($dblink, $dbquery9);
					$tmp9=mysqli_fetch_row($dbresult9); 
				if (count($tmp9) > 0) $security='yes'; else $security='no';
	if($security == 'yes'){
	 echo "<tr><td>$Packname</td><td>$Packvername</td><td>$NewPackvername</td><td>Yes</td><td>$Shortdesc</td></tr>\n";
}else{
	echo "<tr><td>$Packname</td><td>$Packvername</td><td>$NewPackvername</td><td>No</td><td>$Shortdesc</td></tr>\n";
}


}
}
else {
                        echo "No Packages here to display\n";
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
                $dbquery = "select ss.newpackageversion,ss.oldpackageversion,ubp.details,ubp.swdesc,ubp.packagename,ubp.packagever from ubparser as ubp LEFT JOIN packages as ss ON ss.packagename=ubp.packagename where ss.hostname='$servername' and ubp.releasever like 'Ubuntu $Ubureleasever%' and ubp.packagever = ss.newpackageversion order by ubp.packagename ASC;";
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
                        echo "Either No security packages available for this host, or No security errata found...\n";
                }

echo"</table>";
echo "</div>\n";
echo "</div>\n";
}

if($Action == 'transactionlog'){
echo "<div class='contentbox'>\n";
echo "<h2>Transaction Logs for host $servername</h2>";
echo "<div class='content'>\n";
                $dbquery = "SELECT datelog FROM systemstransactionlogs where hostname='$servername' order by datelog DESC";
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
                        echo "No Transactions found ... Make some \n";
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
                        echo "No Transactions found yet...\n";
                }

echo"</table>";
echo "</div>\n";
echo "</div>\n";
}



require_once 'inc/stdfoot.php';

?>
