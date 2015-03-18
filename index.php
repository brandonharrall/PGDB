<?php

	require_once "include/config.php";
	require_once "include/functions.php";
	session_start();
	//If the user has logged in, get their information
	if (isset($_SESSION['user'])) {
		$userName = $_SESSION['user'];
		$userID = $_SESSION['UserID'];
		$userRole = $_SESSION['Role'];
	} else {
		//User is not authed, kick out to login page
		header('location: /pgdb/login.php');
	}
	
	if (isset($_GET['list'])) {
		 $ListType = $_GET['list'];
	} else {
		$ListType = "";
	}
	
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
	<!--<link href="../../common/css/custom.css" rel="stylesheet">-->

  </head>

  <body>
	<?php buildNavBar(true, $userName); ?>
    <div class="container-fluid">
      <div class="row">
		<?php 
			if ($ListType == "missing") {
				buildSideBar("missing");
				echo "\t\t<div class=\"col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main\">\r\n";
				echo "\t\t<h1 class=\"page-header\">Missing Titles</h1>\r\n";
			} else {
				buildSideBar("main");
				echo "\t\t<div class=\"col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main\">\r\n";
				echo "\t\t<h1 class=\"page-header\">All Games</h1>\r\n";
			}
		?>
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
					echo "\t\t\t\t<select name=\"InputSystem\" id=\"InputSystem\" class=\"form-control\">\r\n";
					foreach($b as $w => $test){
						echo "\t\t\t\t\t<option value=\"" . $test . "\">" . $w . "</option>\r\n";
					}
					echo "\t\t\t\t</select>\r\n";
					echo "\t\t\t</div>\r\n";
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
				while($row = $queryResult->fetch_array()) {
					if ($ListType == "missing") {
						if (queryDBDoesUserHaveTitle($con,$row['TitleID'], 1) == "false") {
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

								$numMissing++;
								createButtonAddGameToUser($row['TitleID'], 1, $ListType);
								
							echo "\t\t\t\t\t</td>\r\n";
							echo "\t\t\t\t</tr>\r\n";
						}
					} else {
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
									createButtonAddGameToUser($row['TitleID'], 1, $ListType);
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
	
	<script type="text/javascript">

		var CoverImage = "";
		function UpdateImage(pImage) {
			$("[data-toggle=popover]").popover({placement: 'bottom', trigger: 'hover', content: "<img src=\"image/coverart/" + pImage + "\">", html: true});
			$("[data-toggle=popover]").popover('toggle')
		}
		$("[data-toggle=tooltip]").tooltip();

		//$("[data-toggle=popover]").popover({placement: 'bottom', content: CoverImage, html: true});

	</script>
	<?php
		$con->close();
	?>
	</body>
</html>
