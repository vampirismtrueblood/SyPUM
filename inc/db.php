<?php
// Project SyPUM - Systems Package Update Management
//
//
// Author: Dan Tucny - 12/20/2016 All Rights Reserved
//
// Copyright 2016 Dan Tucny - d@tucny.com
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


function db_connect() {
	$dbhost = '127.0.0.1';
	$dbuser = 'manu1';
	$dbpass = '0000';
	$dbname = 'ubparserdb';

	$dblink = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	if (!$dblink) {
		die('Could not connect to database server. Error: ' . mysqli_error());
	}

	return $dblink;
}

