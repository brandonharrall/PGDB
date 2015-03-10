<?php

//*** In general this should be updated to MySQLI
	
	//Open connection to database using config.php params
	function connect($host, $user, $pass, $db) {

		$link = mysqli_connect($host, $user, $pass, $db);
		return $link;
		
	}

	function queryDBAll($dbobj) {
		$result = mysqli_query($dbobj,"SELECT titl.TitleID, titl.Title, titl.Genre, titl.CoverArt " .
			"FROM titles AS titl " . 
			"WHERE titl.Active = 1 " .
			"ORDER BY titl.Title ASC");
		return $result;
	}
	
	//Returns all systems - Name, ID
	function queryDBSystems($dbobj) {
		$result = mysqli_query($dbobj,"SELECT sys.Name, sys.ID " .
			"FROM system AS sys " . 
			"WHERE 1 " .
			"ORDER BY sys.ID ASC");
		return $result;
	}
	
	
//*** Could be rolled into the query following it
	function queryDBActiveByUser($dbobj,$pUserID) {
		$result = mysqli_query($dbobj,"SELECT uent.EntryID, titl.Title, titl.Genre, uent.Progress, uent.Wanted, uent.Acquired, uent.Priority, uent.Rating, uent.DistroID, dist.ImagePath " .
			"FROM userentries AS uent " . 
			"LEFT JOIN titles AS titl on uent.TitleID = titl.TitleID " .
			"LEFT JOIN distromethod as dist on dist.DistroID = uent.DistroID " .
			"WHERE uent.UserID = " . $pUserID . " AND titl.Active = 1 AND uent.Priority > 0 " .
			"ORDER BY uent.Priority DESC, uent.Rating DESC");
		return $result;
	}
	
	function queryDBArchiveByUser($dbobj,$pUserID) {
		$result = mysqli_query($dbobj,"SELECT uent.EntryID, titl.Title, titl.Genre, uent.Progress, uent.Wanted, uent.Acquired, uent.Priority, uent.Rating, uent.DistroID, dist.ImagePath " .
			"FROM userentries AS uent " . 
			"LEFT JOIN titles AS titl on uent.TitleID = titl.TitleID " .
			"LEFT JOIN distromethod as dist on dist.DistroID = uent.DistroID " .
			"WHERE uent.UserID = " . $pUserID . " AND titl.Active = 1 AND uent.Priority = 0 " .
			"ORDER BY uent.Priority DESC, uent.Rating DESC");
		return $result;
	}

//*** Very heavy hitting query, needs to be complete rewritten
	function queryDBDoesUserHaveTitle($dbobj,$pTitleID,$pUserID) {

		$result = mysqli_query($dbobj,"SELECT EntryID FROM userentries as UENT " .
			"JOIN titles as TITL on TITL.TitleID = UENT.TitleID " .
			"WHERE TITL.TitleID = " . $pTitleID . " " .
			"AND UENT.UserID = " . $pUserID);
		if(mysqli_num_rows($result) == 0) {
			return "false";
		} else {
			return "true";
		}
	}
	
	function queryDistroMethods($dbobj) {
		$result = mysqli_query($dbobj,"SELECT DistroID, Name FROM distromethod");
		return $result;
	}
	
	function queryCountCompleteTitles($dbobj, $archived) {
		if ($archived) {
			$result = mysqli_query($dbobj,"SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress = 100 AND Priority = 0");
		} else {
			$result = mysqli_query($dbobj,"SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress = 100 AND Priority > 0");
		}
		return $result;
	}
	function queryCountInCompleteTitles($dbobj, $archived) {
		if ($archived) {
			$result = mysqli_query($dbobj,"SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress < 100 AND Priority = 0");
		} else {
			$result = mysqli_query($dbobj,"SELECT Count(EntryID) as TitleCount FROM `userentries` WHERE Progress < 100 AND Priority > 0");
		}
		return $result;
	}
	
	function insertTitle($dbobj,$pSystem,$pTitle,$pGenre,$pActive,$pYear) {
		$result = mysqli_query($dbobj, "INSERT INTO `games`.`titles` (`SystemID`, `Title`, `Genre`, `Active`, `ReleaseDate`) " . 
			"VALUES (" . $pSystem . ", '" . $pTitle . "', '" . $pGenre . "', " . $pActive . ", " . $pYear . ");");
		return $result;
	}

	function insertTitleForUser($dbobj,$pTitleID,$pUserID) {
		//TO DO: allow progress, wanted, and acquired
		$result = mysqli_query($dbobj, "INSERT INTO `games`.`userentries` (`TitleID`, `UserID`, `Progress`, `Wanted`, `Acquired`) " .
			"VALUES ('" . $pTitleID . "', '" . $pUserID . "', '0', '0', '1');");
		return $result;
	}
	
	function updateEntryForUser($dbobj,$pEntryID,$pProgress,$pWanted,$pAcquired,$pPriority,$pRating,$pDistro) {
		$result = mysqli_query($dbobj, "UPDATE games.userentries SET Progress=" . $pProgress . ", Wanted = " . $pWanted . ", Acquired = " . $pAcquired . 
			", Priority = " . $pPriority . ", Rating = " . $pRating . 
			", DistroID = " . $pDistro . 
			" WHERE EntryID = " . $pEntryID . ";");
		return $result;
	}
	
	
	function deleteDistroMethod($dbobj,$pDistroID) {
		//$UpdateResult = mysqli_query($dbobj, "UPDATE games.userentries SET DistroID = "
	}
	/*function escape_string($s, $strip_tags = true) {
		if ($strip_tags) $s = strip_tags($s);

		return mysqli_real_escape_string($this->link, $s);
	}*/
	
	/*function query($query, $die_on_error = true) {
		$result = @mysqli_query($this->link, $query);
		if (!$result) {
			$error = @mysqli_error($this->link);

			@mysqli_query($this->link, "ROLLBACK");
			user_error("Query $query failed: " . ($this->link ? $error : "No connection"),
				$die_on_error ? E_USER_ERROR : E_USER_WARNING);
		}

		return $result;
	}*/
	
	//Tidy and close the database connection
	function closeDB() {
		return mysqli_close($link);
	}
?>