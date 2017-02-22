<?php

                require_once './inc/db.php';
//      header("Content-type: image/png");
//$output_file='/tmp/myimage.png';
 $dblink = db_connect();

                // Packages with updates
//                $dbquery2 = "select packagename,count(*) as cunt from packages WHERE packtype='bug' group by packagename order by cunt DESC, packagename ASC;";
		$dbquery2 = "select hostname, sum(if (packtype='bug',1,0)) as bug, sum(if (packtype='sec',1,0)) as sec from packages group by hostname;";
                $dbresult2 = mysqli_query($dblink, $dbquery2);
//              echo "<table>\n";
//              echo "<tr><td>Package</td><td>Short Desc</td><td>Num of Affected servers</td></tr><br/>\n";
                $data=array();
                if ($dbresult2 &&  mysqli_num_rows($dbresult2)) {
                        while ($row2 = mysqli_fetch_object($dbresult2)) {
				$arr_t=array();

				$host_t=$row2->hostname;
				$bug_t=$row2->bug;
				$sec_t=$row2->sec;
				$url_t="https://man.pmalaty.com/view_page.php?action=viewallpkgs&servername=$host_t";
				$arr_t['hostname']=$host_t; 
				$arr_t['bug']=$bug_t; 
				$arr_t['sec']=$sec_t; 
				$arr_t['url']=$url_t;

				//print_r($row2);
				$data[] = $arr_t;
                        }
                }


//print_r($data);
$json_dataset = json_encode($data);

echo $json_dataset;

?>
