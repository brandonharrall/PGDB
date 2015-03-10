<?php

	// *******************************************
	// *** Database configuration (important!) ***
	// *******************************************

	define('DB_TYPE', 'mysql');
	define('DB_HOST', 'localhost');
	define('DB_USER', '');
	define('DB_NAME', 'games');
	define('DB_PASS', '');
	define('DB_PORT', '');
	
	require_once 'mysqli.php';
	
	$con = connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	

?> 
