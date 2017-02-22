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
?>

<div class='contcont'>
        <div class='contentbox'>
                <h2><a href='/servers'>Servers</a></h2>
                <div class='contentboxes'>
<?php

		$dblink = db_connect();
		// All Servers
		$dbquery = "SELECT count(*) FROM systems;";
		$dbresult = mysqli_query($dblink, $dbquery);
		list($servercount) = mysqli_fetch_row($dbresult);
		echo "<a href='/servers' class='dashbox primary-0'><h3>Total servers connected to SyPUM</h3><span class='count'>$servercount</span></a>";

		// Servers with updates
//		$dbquery = "SELECT count(distinct hostname) FROM systems;";
		$dbquery = "SELECT count(distinct hostname) FROM systems where hostname NOT IN (SELECT distinct hostname from packages where packtype='sec')";
		$dbresult = mysqli_query($dblink, $dbquery);
		list($serverupdcount) = mysqli_fetch_row($dbresult);
		echo "<a href='/servers/withbugs' class='dashbox secondary-1-0'><h3>Servers with non-security updates</h3><span class='count'>$serverupdcount</span></a>";

		// Servers with security updates
		$dbquery = "SELECT count(distinct hostname) FROM packages where packtype='sec';";
		$dbresult = mysqli_query($dblink, $dbquery);
		list($serverseccount) = mysqli_fetch_row($dbresult);
		echo "<a href='/servers/withbugsandupdates' class='dashbox red'><h3>Servers with security updates</h3><span class='count'>$serverseccount</span></a>";

		// Servers that haven't checked in recently
		$dbquery = "SELECT count(distinct hostname) FROM systems where checkin <= now() - INTERVAL 1 DAY;";
		$dbresult = mysqli_query($dblink, $dbquery);
		list($servercount) = mysqli_fetch_row($dbresult);
		echo "<a href='/servers/offline' class='dashbox critical'><h3>Servers that haven't checked in the last 24 hrs</h3><span class='count'>$servercount</span></a>";
	
		$dbquery = "SELECT count(distinct hostname) FROM systems where hostname NOT IN (SELECT distinct hostname from packages);";
                $dbresult = mysqli_query($dblink, $dbquery);
                list($servercount) = mysqli_fetch_row($dbresult);
                echo "<a href='/servers/fullyupdated' class='dashbox lime'><h3>Fully Updated Servers</h3><span class='count'>$servercount</span></a>";


?>
		</div>
	</div>
        <div class='contentbox'>
                <h2><a href='/servers'>Packages</a></h2>
                <div class='contentboxes'>
<?php
		// Packages with updates
		$dbquery = "SELECT count(distinct packagename) FROM packages where packtype='bug';";
		$dbresult = mysqli_query($dblink, $dbquery);
		list($servercount) = mysqli_fetch_row($dbresult);
		echo "<a href='/packages/bugspacks' class='dashbox secondary-1-0'><h3>Packages with updates</h3><span class='count'>$servercount</span></a>";

		// Packages with security updates
		$dbquery = "SELECT count(distinct packagename) FROM packages where packtype='sec';";
		$dbresult = mysqli_query($dblink, $dbquery);
		list($servercount) = mysqli_fetch_row($dbresult);
		echo "<a href='/packages/secupdates' class='dashbox red'><h3>Packages with security updates</h3><span class='count'>$servercount</span></a>";

?>

		</div>
	</div>
</div>





        <div class='contentbox'>
                <h2><a href='/servers'>Charts</a></h2>
                <div class='content'>
              <script type="text/javascript" src="/js/amcharts.js"></script>
                <script type="text/javascript" src="/js/serial.js"></script>
        <script src="/js/dataloader.min.js" type="text/javascript"></script>

                <!-- amCharts javascript code -->
                <script type="text/javascript">
                        AmCharts.makeChart("chartdiv",
                                {
                                        "type": "serial",
                                        "categoryField": "hostname",
                                        "rotate": true,
                                        "colors": [
                                                "#FF0000",
                                                "#FF6600"
                                        ],
                                        "startDuration": 1,
                                        "fontFamily": "Sans",
                                        "categoryAxis": {
                                                "gridPosition": "start",
						"autoGridCount": false,
						"gridCount": 1000,
						"listeners": [{
      							"event": "clickItem",
						      "method": function(event) {
						        window.location.href = event.serialDataItem.dataContext.url;
						      }
						    }]
						
                                        },
                                        "trendLines": [],
                                        "graphs": [
                                                {
                                                        "balloonText": "[[title]] of [[category]]:[[value]]",
                                                        "fillAlphas": 1,
                                                        "id": "AmGraph-1",
                                                        "title": "Security",
                                                        "type": "column",
                                                        "valueField": "sec"
                                                },
                                                {
                                                        "balloonText": "[[title]] of [[category]]:[[value]]",
                                                        "fillAlphas": 1,
                                                        "id": "AmGraph-2",
                                                        "title": "Update",
                                                        "type": "column",
                                                        "valueField": "bug"
                                                }
                                        ],
                                        "guides": [],
                                        "valueAxes": [
                                                {
                                                        "id": "ValueAxis-1",
                                                        "stackType": "regular",
                                                        "title": "Number of updates",
                                                }
                                        ],
                                        "allLabels": [],
                                        "balloon": {},
                                        "legend": {
                                                "enabled": true,
                                                "useGraphSettings": true
                                        },
                                        "titles": [
                                                {
                                                        "id": "Title-1",
                                                        "size": 15,
                                                        "text": "Updates by Server"
                                                }
                                        ],
                                        "dataLoader": {
                                                "url": "/getgraph.php",
                                                "format": "json"
                                        }
                                }
                        );
                </script>
 <div id="chartdiv" style="width: 100%; height: <?php echo (($serverupdcount + $serverseccount) * 30) + 80; ?>px ; background-color: #FFFFFF;" ></div>

                </div>
        </div>



