<?php

	//Needed to make a DB conx (used to update image covers below)
	require_once "include/config.php";

	//Require inputs or die
	if (isset($_GET['id']) && isset($_GET['name'])) {																	//Get the list type, used to filter results
		 $titleid = $_GET['id'];
		 $titlename = $_GET['name'];
	} else {
		echo "Improper/missing arguments";
	}

	//Use TheGamesDB for coverart, define the base URL for images
	$baseImgUrl = 'http://thegamesdb.net/banners/';
	$xml_request_url = "http://thegamesdb.net/api/GetGame.php?exactname=" . $titlename;

	//Call the API and get the XML result
	$xml = new SimpleXMLElement($xml_request_url, null, true);
	
	//For each result (<game> tag)
	foreach ($xml->Game as $title) {
	  	//Get each Image > Boxart sub element (TheGamesDB has 2, one for front and back)
	  	foreach($title->Images->boxart as $cover) {
	  		//Get the XML attributes of the boxart
	  		foreach($cover->attributes() as $a => $b) {
	  			//Look for attribute ($a) value ($b) "front"
	  			if ($a == "side" && $b == "front") {
  					//TO DO: ESCAPE GameTitle value
  					//Generate a timestamp for the filename
  					$timestamp = date("YmdGis");
  					//Encode the filename, may cause issues with filesystems
  					$filename = $timestamp . '_' .  urlencode($title->GameTitle) . ".jpg";
  					//If the file is successfully downloaded from the site update the database
  					if (file_put_contents(COVERIMGPATH . $filename, file_get_contents($baseImgUrl . $cover)) <> false) {
						//This should ideally return more useful information,
						//  especially when considering we want to dynamically update the page post ajax.
						echo "File downloaded!</br>";
						updateCoverForTitle($con,$filename,$titleid);
					}
				}
	  		}
  		}
  	}

?>
