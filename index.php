<?php

	require_once "include/config.php";
	require_once "include/functions.php";

/*********************/
/** BEGIN - SESSION **/

	session_start();

	//If the user has logged in, get their information
	if (isset($_SESSION['user'])) {
		$userName = $_SESSION['user'];
		$userID = $_SESSION['UserID'];
		$userRole = $_SESSION['Role'];
	} else {
		//User is not authed, kick out to login page
		header('location: login.php');
	}

/** END - SESSION   **/
/*********************/
	
	if (isset($_GET['list'])) {																	//Get the list type, used to filter results
		 $ListType = $_GET['list'];
	} else {
		$ListType = "";
	}
	
	//Get default distro method for user inserts
	//NOTE: Must be called before adding game to user, contains required information
	$queryResultDistroMethods = queryDistroMethods($con);
	//$distroIDs = array();																		//We'll eventually store this for use later
	while($row = $queryResultDistroMethods->fetch_array()) {
		if ($row['Name'] == "Other") {
			$defaultDistro = $row['DistroID'];
		}
	}
	$queryResultDistroMethods->free();

/*******************/
/** BEGIN - POSTS **/

	if (isset($_POST['InputTitle'])) {															//If the current user is adding a title
		$InsertTitle = $_POST['InputTitle'];													//Get title information
		$InsertGenre = $_POST['InputGenre'];
		$InsertYear = $_POST['InputYear'];
		$InsertSystem = $_POST['InputSystem'];

		insertTitle($con, $InsertSystem, $InsertTitle, $InsertGenre, $userID, $InsertYear);		//Call insert query
	}

	if (isset($_POST['AddGameToUser'])) {														//If the current user is adding a game to their list
		$AddInsertTitle = $_POST['TitleID'];													//Get the title and user
		$AddInsertUserID = $_POST['UserID'];

		insertTitleForUser($con, $AddInsertTitle, $AddInsertUserID, $defaultDistro);			//Call Insert query
	}

/** END - POSTS   **/
/*******************/


	$resAllTitles = queryAllTitles($con);
	$queryCount = $resAllTitles->num_rows;


	$querySystems = queryDBSystems($con);
	$systemsArray = [];
	while($row = $querySystems->fetch_array()) {
		$systemsArray[$row['Name']] = $row['ID'];
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Plavatos' Game Database</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">

  </head>

  <body>
	<?php buildNavBar(true, $userName); ?>
    <div class="container-fluid">
      <div class="row">
		<?php 
			if ($ListType == "missing") {
				buildSideBar("missing");
				echo "\t\t<div class='col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main'>\r\n";
				echo "\t\t<h1 class='page-header'>Missing Titles</h1>\r\n";
			} else {
				buildSideBar("main");
				echo "\t\t<div class='col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main'>\r\n";
				echo "\t\t<h1 class='page-header'>All Games</h1>\r\n";
			}
		?>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-6 placeholder">
            	<h1><?=$queryCount?></h1>
            	games in this database
            </div>
            <div class="col-xs-6 col-sm-6 placeholder">
            </div>
          </div>
<!-- Wrap this with PHP to disable if not admin role -->
			<form method="post" class="form-inline" role="form">
			  <div class="form-group">
				<label class="sr-only" for="InputTitle">Title</label>
				<input type="text" autofocus class="form-control" name="InputTitle" id="InputTitle" placeholder="Enter title">
			  </div>
			  <div class="form-group">
				<label class="sr-only" for="InputGenre">Genre</label>
				<input type="text" class="form-control" name="InputGenre" id="InputGenre" placeholder="Action RPG">
			  </div>
			  <div class="form-group">
				<label class="sr-only" for="InputYear">Year</label>
				<input type="text" class="form-control" name="InputYear" id="InputYear" placeholder="2014">
			  </div>
			  <div class="form-group">
			    <select name="InputSystem" id="InputSystem" class="form-control">
				<?php
					foreach($systemsArray as $systemsName => $systemsID){
						echo "\t\t\t\t\t<option value='$systemsID'>$systemsName</option>\r\n";
					}
				?>
				</select>
			   </div>
			  <button type="submit" class="btn btn-success" action="#">Add to DB</button>
			</form>
<!-- Wrap this with PHP to disable if not admin role -->		  
          <h2 class="sub-header"></h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Title</th>
				  <th>System</th>
                  <th>Genre</th>
				  <th>Year</th>
                  <th>Add</th>
                </tr>
              </thead>
              <tbody>
<?php	

				$numMissing = 0;
				while($row = $resAllTitles->fetch_array()) {
					if ($ListType == "missing") {
						if (queryDBDoesUserHaveTitle($con,$row['TitleID'], $userID) == "false") {
							if ($row['CoverArt'] == "") {
								echo "\t\t\t\t<tr OnClick='getData(" . $row['TitleID'] . ",\"" . $row['Title'] . "\");' OnMouseEnter='changeImage(\"http://placehold.it/120x60\");'>\r\n";
							} else {
								echo "\t\t\t\t<tr OnMouseEnter='changeImage(\"image/coverart/" . $row['CoverArt'] . "\");'>\r\n";
							}
							echo "\t\t\t\t\t<td>" . $row['Title'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>" . $row['SystemName'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>" . $row['Genre'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>" . $row['ReleaseDate'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>\r\n";

								$numMissing++;
								createButtonAddGameToUser($row['TitleID'], $userID, $ListType);
								
							echo "\t\t\t\t\t</td>\r\n";
							echo "\t\t\t\t</tr>\r\n";
						}
					} else {
							if ($row['CoverArt'] == "") {
								echo "\t\t\t\t<tr OnClick='getData(" . $row['TitleID'] . ",\"" . $row['Title'] . "\");' OnMouseEnter='changeImage(\"http://placehold.it/120x60\");'>\r\n";
							} else {
								echo "\t\t\t\t<tr OnMouseEnter='changeImage(\"image/coverart/" . $row['CoverArt'] . "\");'>\r\n";
							}
							echo "\t\t\t\t\t<td>" . $row['Title'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>" . $row['SystemName'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>" . $row['Genre'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>" . $row['ReleaseDate'] . "</td>\r\n";
							echo "\t\t\t\t\t<td>\r\n";

							if (queryDBDoesUserHaveTitle($con,$row['TitleID'], $userID) == "false") {
									$numMissing++;
									createButtonAddGameToUser($row['TitleID'], $userID, $ListType);
							}
							echo "\t\t\t\t\t</td>\r\n";
							echo "\t\t\t\t</tr>\r\n";
					}
				}
?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/docs.min.js"></script>
    <script src="js/sitescripts.js"></script>
	
	<?php
		$con->close();
	?>
	</body>
</html>
