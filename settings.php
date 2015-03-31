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

/******************/
/** BEGIN - GETS **/

	$updatestatus = "";
	$alert = "";
	if (isset($_GET['err'])) {
		$errorNum = $_GET['err'];
		if ($errorNum == 1) {
		 	$alert = "You are not authorized to perform updates!\r\n";
		} else if ($errorNum == 2) {
			$alert = "Failed to update to the most recent schema version!\r\n";
		}
	}

	if (isset($_GET['upd'])) {
		$updatestatus = $_GET['upd'];
	}

/** END - GETS   **/
/******************/

	//Get a recordset of distribution methods for use in the update modal later
	$queryResultDistroMethods = queryDistroMethods($con);

	$distroArray = array();
	while($row = $queryResultDistroMethods->fetch_array()) {
		if ($row['Name'] == "Other") {
			$defaultDistro = $row['DistroID'];
		}
		$distroArray[$row['DistroID']] = $row['Name'];
	}

	//Get a recordset of distribution methods for use in the update modal later
	$queryResultSystems = querySystems($con);

	$systemArray = array();
	while($row = $queryResultSystems->fetch_array()) {
		if ($row['Name'] == "PC") {
			$defaultSystem = $row['ID'];
		}
		$systemArray[$row['ID']] = $row['Name'];
	}



/*******************/
/** BEGIN - POSTS **/

	//If a post was performed to update a users entry send the DB Query to make the update
	if (isset($_POST['RemoveDistro'])) {														//
		$DistroID = $_POST['DistroID'];

		//Call the Update SQL transaction with passed data
		deleteDistroMethod($con, $DistroID, $defaultDistro);
	}

	if (isset($_POST['InputDistro'])) {														//
		$DistroName = $_POST['InputDistro'];

		//Call the Update SQL transaction with passed data
		insertDistroMethod($con, $DistroName);
	}

	if (isset($_POST['InputSystem'])) {														//
		$SystemName = $_POST['InputSystem'];
		if (isset($_POST['InputMfg'])) {
			$MfgName = $_POST['InputMfg'];
		} else {
			$MfgName = "";
		}

		//Call the Update SQL transaction with passed data
		insertSystem($con, $SystemName, $MfgName);
	}

	if (isset($_POST['UpdateAllowReg'])) {
		if (isset($_POST['ChangeAllowReg'])) {
			$ChangeAllowReg = 1;
		} else {
			$ChangeAllowReg = 0;
		}
		//Call the Update SQL transaction with passed data
		updateGlobal($con, 'ALLOW_REGISTRATION', $ChangeAllowReg);
	}

/** END - POSTS   **/
/*******************/


	$dbGlobals = queryGlobals($con);
	if ($dbGlobals !== false) {
		$dbvers 	= $dbGlobals['SCHEMA_VERSION'];
		$dbAllowReg = $dbGlobals['ALLOW_REGISTRATION'];
	} else {
		$dbvers = false;
		$dbAllowReg = false;
	}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Brandon Harrall">
    <link rel="icon" href="../../favicon.ico">

    <title>Plavatos' Game Database</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">

  </head>

  <body>
    <?php buildNavBar(true,$userName); ?>
    <div class="container-fluid">
      <div class="row">
		<?php buildSideBar("none"); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Settings</h1>

		<?php
			echo $alert;

			/** BEGIN ADMIN PANEL **/
			if ($userRole == 1) {
				echo "<h2 class='sub-header'>Admin Settings</h1>\r\n";
				//If the version exists in the database and is lower than the current, present option to update.
				if ($dbvers === false || $dbvers < CURDBVERS ) {
					echo "\t\t\t\t\t\t<form method='post' class='form-inline' role='form' action='update.php'>\r\n";
					echo "<label>A database update needs to be performed. Click Update to begin.</label>\r\n";
					echo "\t\t\t\t\t\t<input type='hidden' id='DBVers' name='DBVers' value=''>\r\n";
					echo "\t\t\t\t\t\t<button id='updatebtn' name='updatebtn' type='submit' class='btn btn-primary'>Update</button>\r\n";
					echo "\t\t\t\t\t\t</form>\r\n";
				} else if ($updatestatus <> "") {
					echo "Database version successfully updated to version <strong>" . $updatestatus . "</strong>\r\n";
				} else {
					echo "The database is currently up to date.\r\n";
				}

				//If the allow registration global exists in the database
				if ($dbAllowReg !== false) {
			        echo "<form method='post' class='form-inline' action='#'>\r\n";
			          echo "<div>\r\n";
			            echo "<div class='checkbox'>\r\n";
					    echo "<label>\r\n";
					    	//Check it's current value to let the user know.
						    if ($dbAllowReg == '1') {
						    	echo "<input id='ChangeAllowReg' name='ChangeAllowReg' type='checkbox' checked> Allow Registration\r\n";
						    } else {
						    	echo "<input id='ChangeAllowReg' name='ChangeAllowReg' type='checkbox'> Allow Registration\r\n";
						    }
					    echo "</label>\r\n";
					  echo "</div>\r\n";
					  echo "\t\t\t\t\t\t<input type='hidden' id='UpdateAllowReg' name='UpdateAllowReg' value=''>\r\n";
					  echo "<button type='submit' class='btn btn-default'>Save</button>\r\n";
					echo "</form>\r\n";
				}
			}

			/** END ADMIN PANEL **/
		?>

          <h2 class="sub-header">Distribution Methods</h2>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
					<th width="20px">&nbsp;</th>
					<th>Distro Method</th>
					<th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>

				<?php
					foreach ($distroArray as $distroID => $distroName) {
						echo "\t\t\t\t<tr>\r\n";
						echo "\t\t\t\t\t<td>&nbsp;</td>\r\n";
						echo "\t\t\t\t\t<td>" . $distroName . "</td>\r\n";
						echo "\t\t\t\t\t<td>\r\n";
						if ($userRole == 1) {
							if ($distroName <> 'Other') {
								echo "\t\t\t\t\t\t<form method='post' class='form-horizontal' role='form'>\r\n";
								echo "\t\t\t\t\t\t<input type='hidden' id='DistroID' name='DistroID' value='" . $distroID . "'>\r\n";
								echo "\t\t\t\t\t\t<button action=\"#\" id='RemoveDistro' name='RemoveDistro' type='submit' class='btn btn-primary'>Delete</button>\r\n";
								echo "\t\t\t\t\t\t</form>\r\n";
							}
						}
						echo "\t\t\t\t\t</td>\r\n";
						echo "\t\t\t\t</tr>\r\n";
					}
				?>
				<?php if ($userRole==1): ?>
				<tr>
					<form method="post" class="form-inline" role="form">
						<td>&nbsp;</td>
						<td>
							<div class="form-group">
								<label class="sr-only" for="InputDistro">Distro</label>
								<input type="text" autofocus class="form-control" name="InputDistro" id="InputDistro" placeholder="New Distro Name">
			  				</div>
			  			</td>
			  			<td>
			  				<button type="submit" class="btn btn-success" action="#">Add</button>
			  			</td>
					</form>
				</tr>
				<?php endif; ?>
              </tbody>
            </table>
          </div> <!-- End Distro Table div -->
		  
          <h2 class="sub-header">Systems</h2>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
					<th width="20px">&nbsp;</th>
					<th>System Name</th>
					<th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>

<?php
					foreach ($systemArray as $systemID => $systemName) {
						echo "\t\t\t\t<tr>\r\n";
						echo "\t\t\t\t\t<td>&nbsp;</td>\r\n";
						echo "\t\t\t\t\t<td>" . $systemName . "</td>\r\n";
						echo "\t\t\t\t\t<td>\r\n";
						echo "\t\t\t\t\t</td>\r\n";
						echo "\t\t\t\t</tr>\r\n";
					}
				?>
				<?php if ($userRole==1): ?>
				<tr>
					<form method="post" class="form-inline" role="form">
						<td>&nbsp;</td>
						<td>
							<div class="form-group">
								<label class="sr-only" for="InputSystem">System</label>
								<input type="text" autofocus class="form-control" name="InputSystem" id="InputSystem" placeholder="New System Name">
			  				</div>
			  			</td>
			  			<td>
			  				<button type="submit" class="btn btn-success" action="#">Add</button>
			  			</td>
					</form>
				</tr>
				<?php endif; ?>

              </tbody>
            </table>
          </div> <!-- End System Table div -->
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/docs.min.js"></script>

	<?php
		$con->close();
	?>
	
  </body>
</html>
