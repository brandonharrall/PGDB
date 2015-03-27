<?php
	
	function tabs($pCount) {
		$strTabs = str_repeat("\t",$pCount);
		return $strTabs;
	}
	
	function createButtonAddGameToUser($pTitleID,$pUserID,$pList) {
		echo "\t\t\t\t\t\t<form method=\"post\">\r\n";
		echo "\t\t\t\t\t\t\t<input type=\"hidden\" id=\"AddGameToUser\" name=\"AddGameToUser\" value=\"1\">\r\n";
		echo "\t\t\t\t\t\t\t<input type=\"hidden\" id=\"UserID\" name=\"UserID\" value=\"" . $pUserID . "\">\r\n";
		echo "\t\t\t\t\t\t\t<input type=\"hidden\" id=\"TitleID\" name=\"TitleID\" value=\"" . $pTitleID . "\">\r\n";
		if ($pList == "missing") {
			echo "\t\t\t\t\t\t\t<button type=\"submit\" class=\"btn btn-default\" action=\"index.php?list=missing\">Add</button>\r\n";
		} else {
			echo "\t\t\t\t\t\t\t<button type=\"submit\" class=\"btn btn-default\" action=\"#\">Add</button>\r\n";
		}
		echo "\t\t\t\t\t\t</form>\r\n";
	}

	function createButtonEditUserEntry($pEntryID) {
		echo "\t\t\t\t\t\t<form method=\"post\">\r\n";
		echo "\t\t\t\t\t\t\t<input type=\"hidden\" id=\"EntryID\" name=\"EntryID\" value=\"" . $pEntryID . "\">\r\n";
		echo "\t\t\t\t\t\t\t<button type=\"submit\" class=\"btn btn-default\" action=\"#\">Add</button>\r\n";
		echo "\t\t\t\t\t\t</form>\r\n";
	}


	function buildNavBar($pLoggedIn,$pUserName) {
		if ($pLoggedIn) {
			echo "<div class=\"navbar navbar-inverse navbar-fixed-top\" role=\"navigation\">\r\n";
			echo "\t\t<div class=\"container-fluid\">\r\n";
			echo "\t\t\t<div class=\"navbar-header\">\t\n";
			echo "\t\t\t\t<button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">\r\n";
			echo "\t\t\t\t\t<span class=\"sr-only\">Toggle navigation</span>\r\n";
			echo "\t\t\t\t\t<span class=\"icon-bar\"></span>\r\n";
			echo "\t\t\t\t\t<span class=\"icon-bar\"></span>\r\n";
			echo "\t\t\t\t\t<span class=\"icon-bar\"></span>\r\n";
			echo "\t\t\t\t</button>\r\n";

			echo "\t\t\t</div>\r\n";
			echo "\t\t\t<div class=\"navbar-collapse collapse\">\r\n";
			echo "\t\t\t\t<ul class=\"nav navbar-nav navbar-right\">\r\n";
			echo "\t\t\t\t\t<li><a href=\"settings.php\">Settings</a></li>\r\n";
			echo "\t\t\t\t\t<li><a href=\"#\">$pUserName</a></li>\r\n";
			echo "\t\t\t\t\t<li><form method='post' class='form-inline' role='form' action='login.php'>\r\n";
			echo "\t\t\t\t\t<input type='hidden' class='form-control' name='logout' id='logout' value=''>\r\n";
			echo "\t\t\t\t\t<button id='logoutbutton' type='submit' class='btn btn-link'>Log Out</button>\r\n";
			echo "\t\t\t\t\t</form></li>\r\n";
			echo "\t\t\t\t</ul>\r\n";
			echo "\t\t\t\t<form class=\"navbar-form navbar-right\">\r\n";
			echo "\t\t\t\t\t<input type=\"text\" class=\"form-control\" placeholder=\"Search...\">\r\n";
			echo "\t\t\t\t</form>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t</div>\r\n";
			echo "\t</div>\r\n";
		} else {
			echo "<div class=\"navbar navbar-inverse navbar-fixed-top\" role=\"navigation\">\r\n";
			echo "\t\t<div class=\"container-fluid\">\r\n";
			echo "\t\t\t<div class=\"navbar-header\">\t\n";
			echo "\t\t\t\t<button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">\r\n";
			echo "\t\t\t\t\t<span class=\"sr-only\">Toggle navigation</span>\r\n";
			echo "\t\t\t\t\t<span class=\"icon-bar\"></span>\r\n";
			echo "\t\t\t\t\t<span class=\"icon-bar\"></span>\r\n";
			echo "\t\t\t\t\t<span class=\"icon-bar\"></span>\r\n";
			echo "\t\t\t\t</button>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t</div>\r\n";
			echo "\t</div>\r\n";
		}
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
		echo "<div class=\"col-sm-3 col-md-2 sidebar\">\r\n";
        echo "\t\t\t<ul class=\"nav nav-sidebar\">\r\n";
        echo "\t\t\t\t<li" . $activeItem[0] . "><a href=\"index.php\">All Games</a></li>\r\n";
        echo "\t\t\t\t<li" . $activeItem[1] . "><a href=\"index.php?list=missing\">Missing</a></li>\r\n";
        //echo "\t\t\t\t<li><a href=\"#\">Reports</a></li>\r\n";
        //echo "\t\t\t\t<li><a href=\"#\">Analytics</a></li>\r\n";
        //echo "\t\t\t\t<li><a href=\"#\">Export</a></li>\r\n";
        echo "\t\t\t</ul>\r\n";
        echo "\t\t\t<ul class=\"nav nav-sidebar\">\r\n";
        echo "\t\t\t\t<li" . $activeItem[2] . "><a href=\"mygames.php\">Active Games</a></li>\r\n";
		echo "\t\t\t\t<li" . $activeItem[3] . "><a href=\"mygames.php?list=archive\">Archived</a></li>\r\n";
        echo "\t\t\t</ul>\r\n";
        echo "\t\t\t<ul class=\"nav nav-sidebar\">\r\n";
        //echo "\t\t\t\t<li><a href=\"\">Another nav item</a></li>\r\n";
        echo "\t\t\t</ul>\r\n";
        echo "\t\t</div>\r\n";
	
	}

	function checkPassword($pPassword1,$pPassword2) {
		if($pPassword1<>$pPassword2) {
			return "<p class='text-warning'>Passwords do not match!</p>";
//Uncomment and expand this section to enforce password length/complexity
//		} else if (strlen($pPassword1) < 8) {
//			return "<p class='text-warning'>Password should be 8 or more characters in length!</p>";
		} else {
			return "valid";
		}
	}

?>