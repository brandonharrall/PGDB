<?php

require_once "include/config.php";
require_once "include/functions.php";

if (isset($_GET['list'])) {
	$ListType = $_GET['list'];
	if ($ListType == "archive") {
		$queryResult = queryDBArchiveByUser($con, 1);
		$activeHeader = "Archived Games";
		//Get the count of both complete and incomplete titles for archived games
		$queryGamesIncomplete = queryCountInCompleteTitles($con, True);
		$queryGamesComplete = queryCountCompleteTitles($con, True);
	}
} else {
	$ListType = "mygames";
	$activeHeader = "Active Games";
	$queryResult = queryDBActiveByUser($con, 1);
	$queryCount = $queryResult->num_rows;
	
	//Get the count of both complete and incomplete titles for active games
	$queryGamesIncomplete = queryCountInCompleteTitles($con, False);
	$queryGamesComplete = queryCountCompleteTitles($con, False);
}

//If a post was performed to update a users entry send the DB Query to make the update
if (isset($_POST['UpdateUserEntry'])) {
	$UpdateEntryID = $_POST['UpdateUserEntry'];
	$UpdateProgress = $_POST['InputComplete'];
	$UpdateDistro = $_POST['InputDistro'];
	
	if (isset($_POST['InputWanted'])) {
		//Interpret the HTML objects 'value' of "on" to 1 or 0
		if ($_POST['InputWanted'] == "on") {
			$UpdateWanted = 1;
		}
	} else {
			$UpdateWanted = 0;
	}
	if (isset($_POST['InputAcquired'])) {
		//Interpret the HTML objects 'value' of "on" to 1 or 0
		if ($_POST['InputAcquired'] == "on") {
			$UpdateAcquired = 1;
		}
	} else {
			$UpdateAcquired = 0;
	}
	if (isset($_POST['optionsPriorities'])) {
		$UpdatePriority = $_POST['optionsPriorities'];
	}
	$UpdateRating = $_POST['InputRating'];
	//Call the Update SQL transaction with passed data
	updateEntryForUser($con, $UpdateEntryID, $UpdateProgress, $UpdateWanted, $UpdateAcquired, $UpdatePriority, $UpdateRating, $UpdateDistro);
}

//Get a recordset of distribution methods for use in the update modal later
$queryResultDistroMethods = queryDistroMethods($con);

$distroIDs = array();
while($row = $queryResultDistroMethods->fetch_array()) {
	if ($row['Name'] == "Other") {
		$defaultDistro = $row['DistroID'];
	}
	$distroIDs[$row['DistroID']] = $row['Name'];
}
$queryResultDistroMethods->free();

//Pull the results from the recordset above to get the counts of total games in the list, completed, and in progress
while($row = $queryGamesIncomplete->fetch_array()) {
	$gamesIncomplete = $row['TitleCount'];
}
$queryGamesIncomplete->free();

while($row = $queryGamesComplete->fetch_array()) {
	$gamesComplete = $row['TitleCount'];
}
$queryGamesComplete->free();

$gamesTotal = $gamesIncomplete + $gamesComplete;

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
		<?php buildSideBar($ListType); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header"><?php echo $activeHeader; ?></h1>
		  
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-6 placeholder">
			  <div class="progress">
			  	  <div class="progress-bar progress-bar-danger" style="width: <?php echo ($gamesIncomplete / $gamesTotal) * 100; ?>%">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				  </div>
				  <div class="progress-bar progress-bar-success" style="width: <?php echo ($gamesComplete / $gamesTotal) * 100; ?>%">
					<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				  </div>
				</div>
            </div>
          </div>

          <h2 class="sub-header">Your Games</h2>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
					<th width="20px">&nbsp;</th>
					<th>Title</th>
					<th>Genre</th>
					<th>Progress</th>
					<th width="30px">Wnt</th>
					<th width="30px">&nbsp;</th>
					<th width="30px">Pri</th>
					<th width="30px">Rtg</th>
					<th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
<?php	
				//Keep track of stats
				$gamesWanted = 0;
				$gamesAcquired = 0;
				$gamesHighPriority = 0;
				
				//$gamesDistro = [];
				while($row = $queryResult->fetch_array()) {
				  //$gamesDistro[$row['DistroID']]++;
					echo tabs(4) . "<tr>\r\n";
					echo tabs(5) . "<td>" . $row['ImagePath'] . "</td>\r\n";
					echo tabs(5) . "<td>" . $row['Title'] . "</td>\r\n";
					echo tabs(5) . "<td>" . $row['Genre'] . "</td>\r\n";
					echo tabs(5) . "<td>\r\n";
					echo tabs(6) . "<div class=\"progress\">\r\n";
					echo tabs(7) . "<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"" . $row['Progress'] . "\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: ". $row['Progress'] . "%;\">";
					echo $row['Progress'] . "%</div>\t\n";
					echo tabs(6) . "</div>\r\n";
					echo tabs(5) . "</td>\r\n";
				  if ($row['Wanted'] == 1) {
					$gamesWanted++;
					echo tabs(5) . "<td><span class=\"glyphicon glyphicon-bookmark\"></span></td>\r\n";
				  } else {
				    echo tabs(5) . "<td><span></span></td>\r\n";
				  }
				  if ($row['Acquired'] == 1) {
					$gamesAcquired++;
					echo tabs(5) . "<td><span class=\"glyphicon glyphicon-check\"></span></td>\r\n";
				  } else {
				    echo tabs(5) . "<td><span></span></td>\r\n";
				  }
					switch ($row['Priority']) {
						case 3:
							$gamesHighPriority++;
							echo tabs(5) . "<td><span class=\"glyphicon glyphicon-fire\"></span></td>\r\n";
							break;
						case 2:
							echo tabs(5) . "<td><span class=\"glyphicon glyphicon-circle-arrow-up\"></span></td>\r\n";
							break;
						case 1:
							echo tabs(5) . "<td></td>\r\n";
							break;
						case 0:
							echo tabs(5) . "<td><span class=\"glyphicon glyphicon-inbox\"></span></td>\r\n";
							break;
					}
				  echo tabs(5) . "<td>" . $row['Rating'] . "</td>\r\n";
				  echo tabs(5) . "<td><button type=\"submit\" class=\"btn btn-info\" " . 
						"data-toggle=\"modal\" data-target=\".bs-example-modal-sm\" " .
						"action=\"#\" onclick=\"UpdateModal(" . $row['EntryID'] . ",'" . $row['Title'] . "'," . $row['Progress'] . 
						"," . $row['Wanted'] . "," . $row['Acquired'] . "," . $row['Priority'] . 
						"," . $row['Rating'] . "," . $row['DistroID'] . ");\">Edit</button></td>\r\n";
				  echo tabs(4) . "</tr>\r\n";
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
	
	<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form method="post" class="form-horizontal" role="form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 id="ModalTitle" class="modal-title">Edit Entry</h4>
				</div>
				<div class="modal-body" style="padding-left:35px">
					<div class="form-group">
					  <div class="col-xs-4 form-group">
						<label for="InputComplete">% Complete</label>
						<input type="text" class="form-control" name="InputComplete" id="InputComplete" placeholder="100">
					  </div>
					</div>
					<div class="form-group">
					  <div class="checkbox">
						<label>
						  <input type="checkbox" name = "InputWanted" id="InputWanted"> Wanted
						</label>
					  </div>
					  <div class="checkbox">
						<label>
						  <input type="checkbox" name = "InputAcquired" id="InputAcquired"> Acquired
						</label>
					  </div>
					</div>
					<div class="form-group">
						<label for="PriorityOptions">Priority</label>
						<div name="PriorityOptions" class="radio">
						  <label>
							<input type="radio" name="optionsPriorities" id="optionsPriorityHigh" value="3">
							High
						  </label>
						</div>
						<div class="radio">
						  <label>
							<input type="radio" name="optionsPriorities" id="optionsPriorityMed" value="2">
							Medium
						  </label>
						</div>
						<div class="radio">
						  <label>
							<input type="radio" name="optionsPriorities" id="optionsPriorityLow" value="1">
							Low
						  </label>
						</div>
						<div name="PriorityOptions" class="radio">
						  <label>
							<input type="radio" name="optionsPriorities" id="optionsPriorityArchive" value="0">
							Archive
						  </label>
						</div>
					</div>
					<div class="form-group">
					  <div class="col-xs-4 form-group">
						<label for="InputRating">Rating</label>
						<input type="text" class="form-control" name="InputRating" id="InputRating" placeholder="0">
					  </div>
					</div>
					<div class="col-xs-12 form-group">
						<select name="InputDistro" id="InputDistro" class="form-control">
						<?php
							foreach ($distroIDs as $key => $value) {
								echo tabs(7) . "<option value=\"" . $key ."\">" . $value . "</option>\r\n";
							}
						?>
						</select>
					</div>
					<div class="form-group">
					  <div class="col-xs-12 form-group">
						<hr>
						<input type="hidden" id="UpdateUserEntry" name="UpdateUserEntry" value="">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save changes</button>
					  </div>
					</div>
				</div>
			</form>
		</div>
	  </div>
 </div>

	<?php
		$con->close();
	?>
	
  </body>
</html>
