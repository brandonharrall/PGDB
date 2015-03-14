<?php
require_once "include/config.php";
require_once "include/functions.php";

//Get a recordset of distribution methods for use in the update modal later
$queryResultDistroMethods = queryDistroMethods($con);

$distroIDs = array();
while($row = mysqli_fetch_array($queryResultDistroMethods)) {
	if ($row['Name'] == "Other") {
		$defaultDistro = $row['DistroID'];
	}
	$distroIDs[$row['DistroID']] = $row['Name'];
}

//If a post was performed to update a users entry send the DB Query to make the update
if (isset($_POST['RemoveDistro'])) {
	$DistroID = $_POST['DistroID'];

	//Call the Update SQL transaction with passed data
	deleteDistroMethod($con, $DistroID, $defaultDistro);
}

/*
//Pull the results from the recordset above to get the counts of total games in the list, completed, and in progress
while($row = mysqli_fetch_array($queryGamesIncomplete)) {
	$gamesIncomplete = $row['TitleCount'];
}
while($row = mysqli_fetch_array($queryGamesComplete)) {
	$gamesComplete = $row['TitleCount'];
}
$gamesTotal = $gamesIncomplete + $gamesComplete;*/
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
    <?php buildNavBar(); ?>
    <div class="container-fluid">
      <div class="row">
		<?php buildSideBar("none"); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Settings</h1>

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
				foreach ($distroIDs as $key => $value) {

					echo "\t\t\t\t<tr>\r\n";

					echo "\t\t\t\t\t<td>&nbsp;</td>\r\n";
					echo "\t\t\t\t\t<td>" . $value . "</td>\r\n";
					echo "\t\t\t\t\t<td>\r\n";
					if ($value <> 'Other') {
						echo "\t\t\t\t\t\t<form method='post' class='form-horizontal' role='form'>\r\n";
						echo "\t\t\t\t\t\t<input type='hidden' id='DistroID' name='DistroID' value='" . $key . "'>\r\n";
						echo "\t\t\t\t\t\t<button action=\"#\" id='RemoveDistro' name='RemoveDistro' type='submit' class='btn btn-primary'>Delete</button>\r\n";
						echo "\t\t\t\t\t\t</form>\r\n";
					}
					echo "\t\t\t\t\t</td>\r\n";
					echo "\t\t\t\t</tr>\r\n";
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
    <script src="../../common/js/bootstrap.js"></script>
	<script src="../../common/js/canvasjs.min.js"></script>
    <script src="../../common/js/docs.min.js"></script>

	<?php
		$con->close();
	?>
	
  </body>
</html>
