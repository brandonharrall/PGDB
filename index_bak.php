<?php

	require_once "config.php";
	require_once "functions.php";

	session_start();
	//$_SESSION['pwd'] = value;
	//Unset: unset($_SESSION['pwd']);

	//End session:
	//$_SESSION = array();
	//session_destroy();
	error_reporting(E_ALL ^ E_NOTICE);
	if (isset($_POST['InputTitle'])) {
		$InsertTitle = $_POST['InputTitle'];
		$InsertGenre = $_POST['InputGenre'];
		$InsertYear = $_POST['InputYear'];
		$InsertSystem = $_POST['InputSystem'];
		insertTitle($con, $InsertSystem, $InsertTitle, $InsertGenre, "1", $InsertYear);
	}

	if (isset($_POST['AddGameToUser'])) {
		$AddInsertTitle = $_POST['TitleID'];
		$AddInsertUserID = $_POST['UserID'];
		insertTitleForUser($con, $AddInsertTitle, $AddInsertUserID);
	}

	//$queryResult = queryDBAllByUser($con, 1);
	$queryResult = queryDBAll($con);
	$queryCount = $queryResult->num_rows;

	$querySystems = queryDBSystems($con);

	$b = [];
	while($row = mysqli_fetch_array($querySystems)) {
		$b[$row['Name']] = $row['ID'];
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

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!--<script src="../../assets/js/ie-emulation-modes-warning.js"></script>-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
	<?php buildNavBar(); ?>
    <div class="container-fluid">
      <div class="row">
		<?php buildSideBar("main"); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">All Games</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-6 placeholder">
			  <div id="chartContainer" style="height: 300px; width: 100%;"></div>
            </div>
            <div class="col-xs-6 col-sm-6 placeholder">
				<div id="chartContainer2" style="height: 300px; width: 100%;"></div>
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
<?php
	echo "\t\t\t<div class=\"form-group\">\r\n";
	echo "\t\t\t\t\t\t<select name=\"InputSystem\" id=\"InputSystem\" class=\"form-control\">\r\n";
	foreach($b as $w => $test){
		echo "\t\t\t\t\t\t\t<option value=\"" . $test . "\">" . $w . "</option>\r\n";
	}
	echo "\t\t\t\t\t\t</select>\r\n";
	echo "\t\t\t\t\t</div>\r\n";
?>
			  <button type="submit" class="btn btn-default" action="#">Add</button>
			</form>
<!-- Wrap this with PHP to disable if not admin role -->		  
          <h2 class="sub-header"></h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Genre</th>
                  <th>Add</th>
                </tr>
              </thead>
              <tbody>
<?php	
				$a = [];
				$numMissing = 0;
				while($row = mysqli_fetch_array($queryResult)) {
				  echo "\t\t\t\t<tr>\r\n";
				  if ($row['CoverArt'] == "") {
						echo "\t\t\t\t\t<td></td>\r\n";
					} else {
						echo "\t\t\t\t\t<td><button onclick=\"UpdateImage('" . $row['CoverArt'] . "')\" type=\"button\" class=\"btn btn-sm btn-info\" data-toggle=\"popover\" title=\"Cover\">Cover</button></td>\r\n";
					}
				  echo "\t\t\t\t\t<td><span data-toggle=\"tooltip\" title=\"Default tooltip\">" . $row['Title'] . "</span></td>\r\n";
				  echo "\t\t\t\t\t<td>" . $row['Genre'] . "</td>\r\n";
				  echo "\t\t\t\t\t<td>\r\n";
					if (!$row['Genre'] == "") {
						$a[$row['Genre']]++;
					}
				  if (queryDBDoesUserHaveTitle($con,$row['TitleID'], 1) == "false") {
						$numMissing++;
						createButtonAddGameToUser($row['TitleID'], 1);
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
    <script src="js/bootstrap.js"></script>
	<script src="js/canvasjs.min.js"></script>
    <script src="js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
	
	<script type="text/javascript">
		window.onload = function () {
			var chart = new CanvasJS.Chart("chartContainer",
			{
				title:{
					text: "In Your Database"
				},
				legend:{
					verticalAlign: "bottom",
					horizontalAlign: "center"
				},
				data: [
				{        
					indexLabelFontSize: 12,
					indexLabelFontFamily: "Monospace",       
					indexLabelFontColor: "darkgrey", 
					indexLabelLineColor: "darkgrey",        
					indexLabelPlacement: "outside",
					type: "pie",       
					//showInLegend: true,
					toolTipContent: "{y} - <strong>#percent%</strong>",
					dataPoints: [
						{  y: <?php echo ($queryCount - $numMissing) ?>},
						{  y: <?php echo $numMissing ?>,exploded: true, indexLabel: "Missing" }

					]
				}
				]
			});
			var chart2 = new CanvasJS.Chart("chartContainer2",
			{
				title:{
					text: "By Genre"
				},
				legend:{
					verticalAlign: "bottom",
					horizontalAlign: "center"
				},
				data: [
				{        
					indexLabelFontSize: 14,
					indexLabelFontFamily: "Monospace",       
					indexLabelFontColor: "darkgrey", 
					indexLabelLineColor: "darkgrey",        
					indexLabelPlacement: "outside",
					type: "pie",       
					//showInLegend: true,
					toolTipContent: "{y} - <strong>#percent%</strong>",
					dataPoints: [
					<?php
						$other = 0;
						foreach($a as $v => $value){
							if ($value > 10) {
								echo "\t\t\t\t\t\t{  y: " . $value . ", indexLabel:\"" . $v . "\" },\r\n";
							} else {
								$other += $value;
							}
						}
					?>
						{  y: <?php echo $other; ?>, legendText:"", indexLabel: "Other" }
					]
				}
				]
			});
			chart.render();
			chart2.render();
		}
		var CoverImage = "";
		function UpdateImage(pImage) {
			$("[data-toggle=popover]").popover({placement: 'bottom', trigger: 'hover', content: "<img src=\"image/coverart/" + pImage + "\">", html: true});
			$("[data-toggle=popover]").popover('toggle')
		}
		$("[data-toggle=tooltip]").tooltip();

		//$("[data-toggle=popover]").popover({placement: 'bottom', content: CoverImage, html: true});

	</script>
	<?php
		mysqli_close($con);
	?>
  </body>

</html>
