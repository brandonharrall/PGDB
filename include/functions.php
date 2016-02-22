<?php

	function tabs($pCount) {
		$strTabs = str_repeat("\t",$pCount);
		return $strTabs;
	}

	function createButtonAddGameToUser($pTitleID,$pUserID,$pList) {
		if ($pList == "missing") {
			$action = "index.php?list=missing";
		} else {
			$action = "#";
		}
		$AddButton =<<<BUTTON
						<form method="post">
							<input type="hidden" id="AddGameToUser" name="AddGameToUser" value="1">
							<input type="hidden" id="UserID" name="UserID" value="$pUserID">
							<input type="hidden" id="TitleID" name="TitleID" value="$pTitleID">
							<button type="submit" class="btn btn-default" action="$action">Add</button>
						</form>

BUTTON;
	echo $AddButton;
	}

/*	function createButtonEditUserEntry($pEntryID) {
		$userButton =<<<BUTTON
		<form method="post">
			<input type="hidden" id="EntryID" name="EntryID" value="" . $pEntryID . "">
			<button type="submit" class="btn btn-default" action="#">Add</button>
		</form>
BUTTON;
		echo $userButton;
	}*/


	function buildNavBar($pLoggedIn,$pUserName) {
		if ($pLoggedIn) {
			$navBar =<<<NAVBAR
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="settings.php">Settings</a></li>
					<li><a href="#">$pUserName</a></li>
					<li><form method='post' class='form-inline' role='form' action='login.php'>
					<input type='hidden' class='form-control' name='logout' id='logout' value=''>
					<button id='logoutbutton' type='submit' class='btn btn-link'>Log Out</button>
					</form></li>
				</ul>
				<form class="navbar-form navbar-right">
					<input type="text" class="form-control" placeholder="Search...">
				</form>
			</div>
		</div>
	</div>

NAVBAR;
		} else {
			$navBar =<<<NAVBAR
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
		</div>
	</div>

NAVBAR;
		}
		echo $navBar;
	}
	
	function buildSideBar($pActive) {
		$activeItem = array("","","","");
		switch ($pActive) {
			case "main":
				$activeItem[0] = " class=\"active\"";
				break;
			case "missing":
				$activeItem[1] = " class=\"active\"";
				break;
			case "mygames":
				$activeItem[2] = " class=\"active\"";
				break;
			case "archive":
				$activeItem[3] = " class=\"active\"";
				break;
		}
		$sideBar =<<<SIDEBAR
		<div class="col-sm-3 col-md-2 sidebar">
        	<ul class="nav nav-sidebar">
        		<li$activeItem[0]><a href="index.php">All Games</a></li>
        		<li$activeItem[1]><a href="index.php?list=missing">Missing</a></li>
        	</ul>
        	<ul class="nav nav-sidebar">
        		<li$activeItem[2]><a href="mygames.php">Active Games</a></li>
				<li$activeItem[3]><a href="mygames.php?list=archive">Archived</a></li>
        	</ul>
        	<ul class="nav nav-sidebar">
        	</ul>

        	<div id="main_img">
			    <img id="gamecover" class="img-thumbnail" src="http://placehold.it/110x110">
			</div>
        </div>

SIDEBAR;
echo $sideBar;
	}

?>