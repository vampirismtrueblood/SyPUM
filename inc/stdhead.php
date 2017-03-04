<!DOCTYPE HTML>
<html>
	<head>
		<title>SyPUM</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="/css/main.css">
<!--		<link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css"> -->
		<link rel="stylesheet" href="/fa/css/font-awesome.min.css">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="https://man.pmalaty.com/img/icons/apple-touch-icon-57x57.png" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://man.pmalaty.com/img/icons/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://man.pmalaty.com/img/icons/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://man.pmalaty.com/img/icons/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon-precomposed" sizes="60x60" href="https://man.pmalaty.com/img/icons/apple-touch-icon-60x60.png" />
		<link rel="apple-touch-icon-precomposed" sizes="120x120" href="https://man.pmalaty.com/img/icons/apple-touch-icon-120x120.png" />
		<link rel="apple-touch-icon-precomposed" sizes="76x76" href="https://man.pmalaty.com/img/icons/apple-touch-icon-76x76.png" />
		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://man.pmalaty.com/img/icons/apple-touch-icon-152x152.png" />
		<link rel="icon" type="image/png" href="https://man.pmalaty.com/img/icons/favicon-196x196.png" sizes="196x196" />
		<link rel="icon" type="image/png" href="https://man.pmalaty.com/img/icons/favicon-96x96.png" sizes="96x96" />
		<link rel="icon" type="image/png" href="https://man.pmalaty.com/img/icons/favicon-32x32.png" sizes="32x32" />
		<link rel="icon" type="image/png" href="https://man.pmalaty.com/img/icons/favicon-16x16.png" sizes="16x16" />
		<link rel="icon" type="image/png" href="https://man.pmalaty.com/img/icons/favicon-128.png" sizes="128x128" />
		<meta name="application-name" content="&nbsp;"/>
		<meta name="msapplication-TileColor" content="#FFFFFF" />
		<meta name="msapplication-TileImage" content="https://man.pmalaty.com/img/icons/mstile-144x144.png" />
		<meta name="msapplication-square70x70logo" content="https://man.pmalaty.com/img/icons/mstile-70x70.png" />
		<meta name="msapplication-square150x150logo" content="https://man.pmalaty.com/img/icons/mstile-150x150.png" />
	</head>
	<body>
		<header>
			<div style='float: left;'>
				<a href='/'><img alt='PMSpider' src='/img/pmSpider.png' height='100px' width='280px'></a><span style='font-size:3em;'>SyPUM</span>
				<?php if ($_SERVER['SERVER_NAME'] == 'man.peter.lan') echo "<span style='font-size:3em;color:red;'>DEV</span>"; ?>
			</div>
			<div style='float: left; line-height:100px;'>
				<a href='/' style='text-decoration:none;'><h1 style='font-size:4em;' class='sig1inv'>SyPUM</h1></a>
			</div>

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


session_start();
session_cache_limiter('nocache');
$cache_limiter = session_cache_limiter();

$startts = microtime(TRUE);
require_once 'inc/db.php';
require_once 'inc/auth.php';

if ($section != '') {
	echo "<div style='clear: both;'></div>\n";
}
?>
		</header>
<div style='clear: both;'></div>

