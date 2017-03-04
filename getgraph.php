<?php
session_start();
session_cache_limiter('nocache');
$cache_limiter = session_cache_limiter();

if (!isset($_SESSION['loggedin'])) exit();

require_once 'inc/db.php';
$dblink = db_connect();
// Packages with updates
$dbquery2 = "select hostname, sum(if (packtype='bug',1,0)) as bug, sum(if (packtype='sec',1,0)) as sec from packages group by hostname;";
$dbresult2 = mysqli_query($dblink, $dbquery2);
$data=array();
if ($dbresult2 &&  mysqli_num_rows($dbresult2)) {
	while ($row2 = mysqli_fetch_object($dbresult2)) {
		$arr_t=array();

		$host_t=$row2->hostname;
		$bug_t=$row2->bug;
		$sec_t=$row2->sec;
		$url_t="/view_page.php?action=viewallpkgs&servername=$host_t";
		$arr_t['hostname']=$host_t; 
		$arr_t['bug']=$bug_t; 
		$arr_t['sec']=$sec_t; 
		$arr_t['url']=$url_t;

		$data[] = $arr_t;
	}
}

$json_dataset = json_encode($data);

echo $json_dataset;

?>
