		<footer>
			<div class='contcont'>
			<div class='contentbox'>
				<h2>Connection Info</h2>
			        <div class='contentboxes'>

<?php
// Project SyPUM - Systems Package Update Management
//
//
// Author: Dan Tucny - 12/20/2016 All Rights Reserved
//
// Copyright 2016 Dan Tucny 
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


                if (filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $ip = 4;
                } elseif (filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                        $ip = 6;
                } else {
                        $ip = 0;
                }

		$addr = str_replace('.', '&shy;.', $_SERVER["REMOTE_ADDR"]);
		$addr = str_replace(':', '&shy;:', $addr);

		echo "<span class='dashbox primary-0 db2w'><h3>IP</h3>{$_SERVER["REMOTE_ADDR"]}<span>";
		switch ($ip) {
			case '4':
				echo "IPv4";
				break;
			case '6':
				echo "IPv6";
				break;
			default:
				echo "invalid";
				break;
		}
		echo "</span></span>\n";
/*
                $org = geoip_org_by_name($_SERVER["REMOTE_ADDR"]);
                if ($org) {
                    echo "<span class='dashbox primary-0'><h3>Org</h3><span>$org</span></span>";
                }

                $isp = geoip_isp_by_name($_SERVER["REMOTE_ADDR"]);
                if ($isp) {
                    echo "<span class='dashbox primary-0'><h3>ISP</h3>$isp</span>\n";
                }
*/
		$country = geoip_country_name_by_name($_SERVER["REMOTE_ADDR"]);
		if ($country) {
			echo "<span class='dashbox primary-0'><h3>Country</h3>$country</span>\n";
		}

		echo "<span class='dashbox primary-0'><h3>Encryption</h3>";
		if ($_SERVER["HTTPS"] = 'on') echo "Yes!"; else echo "No! :(";
		echo "</span>";

		echo "<span class='dashbox primary-0'><h3>Protocol</h3>{$_SERVER["SERVER_PROTOCOL"]}</span>\n";
?>
				</div>
			</div>
			</div>

			<div style='clear: both;'></div>
			<div id="copyright">
				&copy; <?php echo date('Y'); ?> AcqICT. All rights reserved.
			</div>
			<div id="runtime">
<?php
$endts = microtime(TRUE);
$runtime = number_format($endts - $startts, 2);
echo "\t\t\t\tRuntime: {$runtime}s\n";
?>
			</div>
		</footer>
	</body>
</html>
