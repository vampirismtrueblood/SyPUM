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


switch ($page) {
        case 'bugspacks':
                $title = "List of Bug fix Packs on All Servers";
                $serversqlcond = "WHERE packtype='bug'";
                break;


        case 'secupdates':
                $title = "List of Security Packages on All Servers";
                $serversqlcond = "WHERE packtype='sec'";
                break;

//        default:
//                $title = "Nothing's here";
//                $serversqlcond = "";
//                break;
}

//echo "<h1>Host $servername</h1>";
echo "<div class='contentbox'>\n";
echo "<h2>$title</h2>";
echo "<div class='content'>\n";
//                $dbquery = "SELECT distinct packagename,shortdesc FROM packages $serversqlcond;";
//                $dbresult = mysqli_query($dblink, $dbquery);
$dbquery2 = "select packagename,shortdesc,count(*) as cunt from packages $serversqlcond group by packagename order by cunt DESC, packagename ASC;";
$dbresult2 = mysqli_query($dblink, $dbquery2);
if($serversqlcond == "WHERE packtype='bug'") $pkgtype='bug'; elseif ($serversqlcond == "WHERE packtype='sec'") $pkgtype='sec';
echo "<table>\n";
echo "<tr><td>Package</td><td>Short Desc</td><td>Num of Affected servers</td></tr><br/>\n";
if ($dbresult2 &&  mysqli_num_rows($dbresult2)) {
	while ($row2 = mysqli_fetch_object($dbresult2)) {
		$Packname=$row2->packagename;
		$Shortdesc=$row2->shortdesc;
		$count=$row2->cunt;
		echo "<tr><td>$Packname</td><td>$Shortdesc</td><td><a href='/view_page.php?action=viewsrvspkg&packagename=$Packname&pktype=$pkgtype'>$count</td></tr>\n";

	}
}
else {
	echo "No Packages here to display\n";
}

echo"</table>";
echo "</div>\n";
echo "</div>\n";


require_once 'inc/stdfoot.php';

?>
