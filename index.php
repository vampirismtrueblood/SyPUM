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



$pathparts = explode('/', $_SERVER['PATH_INFO']);
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
require_once 'inc/stdhead.php';

switch ($section) {
	case '':
	default:
		require 'inc/pages/dashboard.php';
		break;
}


require_once 'inc/stdfoot.php';
?>
