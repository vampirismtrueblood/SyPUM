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


require_once ('inc/db.php');

function Redirect_dashboard($url, $permanent = false)
{
    header('Location: ./' . $url, true, $permanent ? 301 : 302);

    exit();
}

$dblink = db_connect();
$Action='';
if (isset($_GET['action']))  $Action=mysqli_real_escape_string($dblink,$_GET['action']);
if (isset($_GET['servername'])) $servername=mysqli_real_escape_string($dblink,$_GET['servername']);

if($Action == 'delserver'){

echo "Will del ten\n";
}

if($Action == 'updateserver'){
$query4="UPDATE updates set pushupdates='yes' where hostname='$servername'";
$dbres4=mysqli_query($dblink, $query4);

}

if($Action == 'cancelupdateserver'){
$query5="UPDATE updates set pushupdates='no' where hostname='$servername'";
$dbres5=mysqli_query($dblink, $query5);
}
if($Action == 'resetupdatestatus'){
$query6="UPDATE updates set updated='no' where hostname='$servername'";
$dbres6=mysqli_query($dblink, $query6);
}


Redirect_dashboard('',false);



?>
