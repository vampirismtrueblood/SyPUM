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

 
// get the HTTP method, path and body of the request
include('inc/db.php');



$method = $_SERVER['REQUEST_METHOD'];
//echo "Method: $method\n";
//$method = $_POST['myfile'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//print_r($request);
$input = json_decode(file_get_contents('php://input'),true);
//echo "$input\n";
$fff=file_get_contents('php://input');
//print_r($fff);
file_put_contents("/var/www/uploads/$request[1]",$fff);
 
// connect to the mysql database
//$link = mysqli_connect('127.0.0.1', 'ubparser1', '0000', 'ubparserdb');
//mysqli_set_charset($link,'latin1');
$dblink = db_connect();
//Break Request into our SQL statements
$command=$request[0];
$Hostname=$request[1];
$IPaddr=$request[2];
$uburelease=$request[3];
$PackageName=$request[4];
$PackageVer=$request[5];
$newPackageVer=$request[6];


$sql="";
// create SQL based on HTTP method
switch ($method) {
  case 'GET':
 if($command == 'updatemasterupgrades'){
/*
		$PackagesinDB=array();
		$Packagestodelete=array();
		$dbquery = "SELECT packagename,packageversion FROM systems where hostname='$Hostname';";
                $dbresult = mysqli_query($dblink, $dbquery);
		while ($row = mysqli_fetch_object($dbresult)) {
		$[]=$row->packagename . $row->packageversion;
		}
*/		$sql="INSERT INTO systems values ('$Hostname','$IPaddr','$uburelease','$PackageName','$PackageVer')";
}
  elseif($command == 'getupdates'){
//	echo "Here\n";
	$sql="SELECT pushupdates from updates where hostname='$Hostname'";
//	$execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'flushmasterupdates'){
//      echo "Here\n";
        $sql="DELETE FROM systems where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'amisubscribed'){
//      echo "Here\n";
        $sql="SELECT hostname FROM updates where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'subscribeme'){
//      echo "Here\n";
        $sql="INSERT INTO updates values ('$Hostname','$IPaddr','$uburelease','no','no','no',NOW())";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'resetpacks'){
//      echo "Here\n";
        $sql="UPDATE updates set resetpacks='yes' where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}

 elseif($command == 'resetupdatestatus'){
//      echo "Here\n";
        $sql="UPDATE updates set pushupdates='no', updated='yes' where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}


  elseif($command == 'doiresetpacks'){
//      echo "Here\n";
        $sql="SELECT resetpacks from updates where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'doneresetpacks'){
//      echo "Here\n";
        $sql="UPDATE updates set resetpacks='no' where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'deletehost'){
//      echo "Here\n";
        $sql="DELETE FROM systems WHERE hostname='$Hostname'; DELETE FROM updates where hostname='$Hostname';";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'pushupdates'){
//      echo "Here\n";
        $sql="UPDATE updates set pushupdates='yes' where hostname='$Hostname'";
//      $execquery=mysqli_query($dblink,$sql);
}
  elseif($command == 'checkin'){
//      echo "Here\n";
        $sql="UPDATE updates set checkin=NOW() where hostname='$Hostname'and ipaddr='$IPaddr' and ubunturelease='$uburelease'";
//      $execquery=mysqli_query($dblink,$sql);
}


 elseif($command == 'updatemastersecupdates'){
//      echo "Here\n";
	       
//                echo "Package Doesn't exist for systemssecurity\n";
	        $sql="INSERT INTO systemssecurity values ('$Hostname','$IPaddr','$uburelease','$PackageName','$PackageVer', '$newPackageVer')";
//      $execquery=mysqli_query($dblink,$sql);

}
 elseif($command == 'flushmastersecupdates'){
//      echo "Here\n";
        $sql="DELETE FROM systemssecurity where hostname='$Hostname'";
//      $execquery=mysqlipushupdatesquery($dblink,$sql);
}











//else {echo "Invalid Command ...  <br />\n";
 //               echo 'usage: api.php/api/$id/$command' . "\n";


//}
break;

  case 'POST':
if($command == 'logfile'){
	$LogFile=file_get_contents("/var/www/uploads/$request[1]");
        $sql="INSERT INTO systemstransactionlogs values ('$Hostname','$IPaddr','$uburelease',NOW(),'$LogFile')";
//      $execquery=mysqli_query($dblink,$sql);
}
break;	   
case 'PUT':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete `$table` where id=$key"; break;
}
 
//echo "SQL: $sql\n";
// excecute SQL statement
//$result = mysqli_query($link,$sql);
$execquery=mysqli_query($dblink,$sql);
if(!$execquery) echo "Error ... " . mysqli_error($dblink) . "\n";
 /*	switch ($command) {

			case 'status':
			$sql = "select name,status from `$table`".($key?" WHERE id=$key":''); break;
			case 'name':
			$sql = "select name,id from `$table`".($key?" WHERE id=$key":''); break;
			case 'pos':
			$sql = "select name,position from `$table`".($key?" WHERE id=$key":''); break;



		}

*/
// die if SQL statement failed
if (!$execquery) {
  http_response_code(404);
  die(mysqli_error());
}
 
// print results, insert id or affected row count
if ($method == 'GET') {
//  if (!$key) echo '[';
  for ($i=0;$i<mysqli_num_rows($execquery);$i++) {
    echo ($i>0?'':'').json_encode(mysqli_fetch_object($execquery));
echo "\n";  
}
//  if (!$key) echo ']';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}
 
// close mysql connection
mysqli_close($link);
