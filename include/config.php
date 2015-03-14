<?php

	// *******************************************
	// *** Database configuration (important!) ***
	// *******************************************

	define('DB_TYPE', 'mysql');
	define('DB_HOST', '');
	define('DB_USER', '');
	define('DB_NAME', 'games');
	define('DB_PASS', '');
	define('DB_PORT', '3306');
	
	require_once 'include/mysqli.php';
	
	$con = connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	

?> 
