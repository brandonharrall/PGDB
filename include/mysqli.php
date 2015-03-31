<?php
	
	//Open connection to database using config.php params
	function connect($host, $user, $pass, $db) {

		$link = new mysqli($host, $user, $pass, $db);
		if ($link->connect_errno) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		} else {
			return $link;
		}

	}

/**         **/
/** SELECTS **/
/**         **/

	//Returns result of passed username/password or false if no results
	function queryLogin($dbobj,$pUsername,$pPassword) {
		$statement1 = $dbobj->prepare("SELECT Salt " . 
			"FROM users " .
			"WHERE UserName=?;"); 
		$statement1->bind_param('s',$pUsername);
		$statement1->execute();
		$saltandpass = $statement1->get_result();
		$statement1->close();

		$saltandpass = $saltandpass->fetch_array();
		$salt = $saltandpass['Salt'];
		$saltedpass = hash("sha256",$salt . $pPassword);

		$statement2 = $dbobj->prepare("SELECT UserID, Role, UserName, Password " . 
			"FROM users " .
			"WHERE Active=1 AND UserName=? AND Password=?;");
		$statement2->bind_param('ss',$pUsername,$saltedpass);
		$statement2->execute();
		$result = $statement2->get_result();
		$statement2->close();
		if($result->num_rows == 1) {
			return $result;
		} else {
			return false;
		}
	}

	function queryForUser($dbobj,$pUserName) {
		$statement = $dbobj->prepare("SELECT COUNT(ID) " . 
			"FROM users " .
			"WHERE Active=1 AND UserName=?;");
		$statement->bind_param('s',$pUsername);
		$statement->execute();
		$result = $statement->get_result();
		$statement->close();
		if($result->num_rows == 1) {
			return false;
		} else {
			return true;
		}
	}
	
	//Returns result of all active titles
	function queryAllTitles($dbobj) {
		$result = $dbobj->query("SELECT titl.TitleID, titl.Title, titl.Genre, titl.CoverArt " .
			"FROM titles AS titl " . 
			"WHERE titl.Active = 1 " .
			"ORDER BY titl.Title ASC");

		return $result;
	}
	
	//Returns result of all systems
	function queryDBSystems($dbobj) {
		$result = $dbobj->query("SELECT sys.Name, sys.ID " .
			"FROM system AS sys " . 
			"WHERE 1 " .
			"ORDER BY sys.ID ASC");
		return $result;
	}
	
	
//*** Could be rolled into the query following it
	function queryDBActiveByUser($dbobj,$pUserID) {
		$result = $dbobj->query("SELECT uent.EntryID, titl.Title, titl.Genre, uent.Progress, uent.Wanted, uent.Acquired, uent.Priority, uent.Rating, uent.DistroID, dist.ImagePath " .
			"FROM userentries AS uent " . 
			"LEFT JOIN titles AS titl on uent.TitleID = titl.TitleID " .
			"LEFT JOIN distromethod as dist on dist.DistroID = uent.DistroID " .
			"WHERE uent.UserID = " . $pUserID . " AND titl.Active = 1 AND uent.Priority > 0 " .
			"ORDER BY uent.Priority DESC, uent.Rating DESC");
		return $result;
	}
	
	function queryDBArchiveByUser($dbobj,$pUserID) {
		$result = $dbobj->query("SELECT uent.EntryID, titl.Title, titl.Genre, uent.Progress, uent.Wanted, uent.Acquired, uent.Priority, uent.Rating, uent.DistroID, dist.ImagePath " .
			"FROM userentries AS uent " . 
			"LEFT JOIN titles AS titl on uent.TitleID = titl.TitleID " .
			"LEFT JOIN distromethod as dist on dist.DistroID = uent.DistroID " .
			"WHERE uent.UserID = " . $pUserID . " AND titl.Active = 1 AND uent.Priority = 0 " .
			"ORDER BY uent.Priority DESC, uent.Rating DESC");
		return $result;
	}

//*** Very heavy hitting query, needs to be complete rewritten
	function queryDBDoesUserHaveTitle($dbobj,$pTitleID,$pUserID) {
		$statement = $dbobj->prepare("SELECT EntryID FROM userentries as UENT " .
			"JOIN titles as TITL on TITL.TitleID = UENT.TitleID " .
			"WHERE TITL.TitleID = ? " .
			"AND UENT.UserID = ?");
		$statement->bind_param('si',$pTitleID,$pUserID);
		$statement->execute();
		$result = $statement->get_result();
		$statement->close();
		if($result->num_rows == 0) {
			return "false";
		} else {
			return "true";
		}
	}
	
	//Returns result of all distribution methods
	function queryDistroMethods($dbobj) {
		$result = $dbobj->query("SELECT DistroID, Name FROM distromethod");
		return $result;
	}

	function querySystems($dbobj) {
		$result = $dbobj->query("SELECT ID, Name, Mfg FROM system");
		return $result;
	}
	
	//Returns result of a count of titles
//*** Should get the first row of the result and return only the count, no need to return result set.
	function queryCountTitles($dbobj, $archived, $complete) {
		if ($archived) {
			if ($complete) {
				$result = $dbobj->query("SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress = 100 AND Priority = 0");
			} else {
				$result = $dbobj->query("SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress < 100 AND Priority = 0");
			}
		} else {
			if ($complete) {
				$result = $dbobj->query("SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress = 100 AND Priority > 0");
			} else {
				$result = $dbobj->query("SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress < 100 AND Priority > 0");
			}
		}
		return $result;
	}

	//Returns array of global variables
	function queryGlobals($dbobj) {
		$result = $dbobj->query("SELECT `NAME`,`VALUE` FROM games.globals;");
		//If last DB result was 'table doesn't exist' (no globals table)
		if ($dbobj->errno == 1146) {
			return false;
		} else {
			$dbvers = [];
			while($row = $result->fetch_array()) {
				$dbvers[$row['NAME']] = $row['VALUE'];
			}
			return $dbvers;
		}
	}

/**         **/	
/** INSERTS **/
/**         **/
	
	function insertTitle($dbobj,$pSystem,$pTitle,$pGenre,$pActive,$pYear) {
		$statement = $dbobj->prepare("INSERT INTO `games`.`titles` (`SystemID`, `Title`, `Genre`, `Active`, `ReleaseDate`) " . 
			"VALUES (?, ?, ?, ?, ?);");
		$statement->bind_param('issii',$pSystem,$pTitle,$pGenre,$pActive,$pYear);
		$statement->execute();
		$statement->close();
	}

	function insertTitleForUser($dbobj,$pTitleID,$pUserID,$pDistroID) {
		//TO DO: allow progress, wanted, and acquired
		$statement = $dbobj->prepare("INSERT INTO `games`.`userentries` (`TitleID`, `UserID`,`DistroID`, `Progress`, `Wanted`, `Acquired`) " .
			"VALUES (?, ?, ?, '0', '0', '1');");
		$statement->bind_param('iii',$pTitleID,$pUserID,$pDistroID);
		$statement->execute();
		$statement->close();
	}
	
	function insertNewUser($dbobj,$pUserName,$pSalt,$pSaltedPass) {
		$statement = $dbobj->prepare("INSERT INTO `games`.`users` (`UserName`, `Password`,`Salt`) " .
			"VALUES (?, ?, ?);");
		$statement->bind_param('sss',$pUserName,$pSaltedPass,$pSalt);
		$statement->execute();
		$statement->close();
	}

	function insertDistroMethod($dbobj,$pDistroName) {
		$statement = $dbobj->prepare("INSERT INTO `games`.`distromethod` (`Name`) " .
			"VALUES (?);");
		$statement->bind_param('s',$pDistroName);
		$statement->execute();
		$statement->close();
	}

	function insertSystem($dbobj,$pSystemName,$pSystemMfg) {
		$statement = $dbobj->prepare("INSERT INTO `games`.`system` (`Name`,`Mfg`) " .
			"VALUES (?, ?);");
		$statement->bind_param('ss',$pSystemName,$pSystemMfg);
		$statement->execute();
		$statement->close();
	}

/**         **/
/** UPDATES **/
/**         **/
	function updateEntryForUser($dbobj,$pEntryID,$pProgress,$pWanted,$pAcquired,$pPriority,$pRating,$pDistro) {
		$statement = $dbobj->prepare("UPDATE games.userentries SET Progress=?, Wanted=?, Acquired=?" . 
			", Priority=?, Rating=?, DistroID=?" . 
			" WHERE EntryID=?;");
		$statement->bind_param('iiiiiii',$pProgress,$pWanted,$pAcquired,$pPriority,$pRating,$pDistro,$pEntryID);
		$statement->execute();
		$statement->close();
	}

	//Given a global variable, update it's value
	function updateGlobal($dbobj,$pGlobalName,$pGlobalValue) {
		$statement = $dbobj->prepare("UPDATE games.globals SET VALUE=?" .
			" WHERE NAME=?;");
		$statement->bind_param('ss',$pGlobalValue,$pGlobalName);
		$statement->execute();
		$statement->close();
	}
	
/**         **/
/** DELETES **/
/**         **/

	//Removes a distribution method, updates all user entries using this method to the passed default
	function deleteDistroMethod($dbobj,$pDistroID,$pDefaultDistro) {
		//Update all distros that match the one being deleted to a passed default
		$updatestmt = $dbobj->prepare("UPDATE games.userentries SET DistroID=? WHERE DistroID=?;");
		$updatestmt->bind_param('ii',$pDefaultDistro,$pDistroID);
		$updatestmt->execute();
		$updatestmt->close();
		
		//Remove distro from DB
		$deletestmt = $dbobj->prepare("DELETE FROM games.distromethod WHERE distromethod.DistroID=?;");
		$deletestmt->bind_param('i',$pDistroID);
		$deletestmt->execute();
		$deletestmt->close();
	}
	
?>