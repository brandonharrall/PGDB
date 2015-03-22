<?php
	
	//Open connection to database using config.php params
	function connect($host, $user, $pass, $db) {

		$link = new mysqli($host, $user, $pass, $db);
		if (mysqli_connect_errno($link)) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		} else {
			return $link;
		}

	}

/**         **/
/** SELECTS **/
/**         **/

	function queryLogin($dbobj,$pUsername,$pPassword) {
		$statement = $dbobj->prepare("SELECT UserID, Role, UserName, Password " . 
			"FROM users " .
			"WHERE Active=1 AND UserName=? AND Password=?;");
		$statement->bind_param('ss',$pUsername,$pPassword);
		$statement->execute();
		$result = $statement->get_result();
		$statement->close();
		if($result->num_rows == 1) {
			return $result;
		} else {
			return false;
		}
	}
	
	function queryDBAll($dbobj) {
		$result = $dbobj->query("SELECT titl.TitleID, titl.Title, titl.Genre, titl.CoverArt " .
			"FROM titles AS titl " . 
			"WHERE titl.Active = 1 " .
			"ORDER BY titl.Title ASC");
		/*$dbArray = array();
		while($row = $result->fetch_array()) {
			$dbArray["ID"] = 	$row['TitleID'];
			$dbArray["Title"] = $row['Title'];
			$dbArray["Genre"] = $row['Genre'];
			$dbArray["Cover"] = $row['CoverArt'];
		}
			var_dump($dbArray[113]);
		
		$result->free();*/
		return $result;
		//return $dbArray;
	}
	
	//Returns all systems - Name, ID
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
	
	function queryDistroMethods($dbobj) {
		$result = $dbobj->query("SELECT DistroID, Name FROM distromethod");
		return $result;
	}
	
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
	
	function updateEntryForUser($dbobj,$pEntryID,$pProgress,$pWanted,$pAcquired,$pPriority,$pRating,$pDistro) {
		$statement = $dbobj->prepare("UPDATE games.userentries SET Progress=?, Wanted=?, Acquired=?" . 
			", Priority=?, Rating=?, DistroID=?" . 
			" WHERE EntryID=?;");
		$statement->bind_param('iiiiiii',$pProgress,$pWanted,$pAcquired,$pPriority,$pRating,$pDistro,$pEntryID);
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