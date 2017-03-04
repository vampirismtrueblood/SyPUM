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
if (isset($_SERVER['PATH_INFO'])) {
	$pathparts = explode('/', $_SERVER['PATH_INFO']); 
} elseif (isset($_SERVER['REDIRECT_PATH_INFO'])) {
	$pathparts = explode('/', $_SERVER['REDIRECT_PATH_INFO']); 
} else {
	$pathparts = array(); 
}
if (isset($pathparts[1])) {
	$section = $pathparts[1];
} else {
	$section = '';
}

if (isset($pathparts[2])) {
	$page = $pathparts[2];
} else {
	$page = '';
}

if ($section == "SyPUMclient") {
	require 'inc/pages/SyPUMclient.php';
	exit;
}

if ($section == "SyPUMcron") {
        require 'inc/pages/SyPUMcron.php';
        exit;
}


if (substr($section, -3) != 'csv') { // If we aren't outputting CSV, display nice headers etc...
	require_once 'inc/stdhead.php';
} else { // If we are outputting CSV, we don't want our pretty headers, but, we still need to access our session, do auth and prep the DB
	session_start();
	session_cache_limiter('nocache');
	$cache_limiter = session_cache_limiter();

	$startts = microtime(TRUE);
	require_once 'inc/db.php';
	require_once 'inc/auth.php';
}
//echo "Server info" . $_SERVER['PATH_INFO'] . "\n";
switch ($section) {
	case 'servers':
	case 'serverscsv':
		require 'inc/pages/servers.php';
		break;
	case 'packages':
		require 'inc/pages/packages.php';
		break;
	case 'SyPUMclient':
		require 'inc/pages/SyPUMclient.php';
		break;
	case 'SyPUMcron':
                require 'inc/pages/SyPUMcron.php';
                break;


	case '':
	default:
		require 'inc/pages/dashboard.php';
		break;
}

if (substr($section, -3) != 'csv') {
	require_once 'inc/stdfoot.php';
}

?>
