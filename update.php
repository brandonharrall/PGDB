<?php
	
	require_once "include/config.php";
	session_start();
	//Check that we're in a session and that the user is an admin
	if (isset($_SESSION['user'])) {
		$userRole = $_SESSION['Role'];
	} else {
		//User is not authed, kick out to login page
		//header('location: settings.php');
	}

	//User is not an admin, kick out
	if ($userRole <> 1) {
		header('location: settings.php?err=1');	
	}

	//Get current version from database, if inital run and no global table, version is false
	echo "Getting current DB version...</br>";
	$dbGlobals = queryGlobals($con);
	if ($dbGlobals !== false) {
		$dbvers 	= $dbGlobals['SCHEMA_VERSION'];
	} else {
		echo "No schema version found</br>";
		$dbvers = false;
	}

	//If there is no version create the globals table and global values
	if ($dbvers === false) {

		//Verify Schema version in Database
		$dbGlobals = queryGlobals($con);
		if ($dbGlobals !== false) {
			$dbvers 	= $dbGlobals['SCHEMA_VERSION'];
		} else {
			$dbvers = false;
		}
	}

	//Check that it's up to date before returning to settings
	if ($dbvers == CURDBVERS) {
		echo "All done!</br>";
		header('location: settings.php?upd=' . $dbvers);	
	} else {
		header('location: settings.php?err=2');
	}
?>