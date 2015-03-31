<?php

	require_once "include/config.php";
	require_once "include/functions.php";

	$dbGlobals = queryGlobals($con);
	if ($dbGlobals !== false) {
		$dbAllowReg = $dbGlobals['ALLOW_REGISTRATION'];
	} else {
		$dbAllowReg = false;
	}

	$alert = "";
	session_start();
	//received logout request
	if (isset($_POST['logout'])) {
		//unset vars, destroy session, present login page again
		$_SESSION = array();
		session_destroy();
	} 


	//Check if the user has been authenticated
	if(isset($_SESSION['user'])) {
		//Send to index if so
		header('location: index.php');
	} else {
		//If the user has not been authenticated
		//Ensure that allow registration is enabled in case a post is fabricated
		if (isset($_POST['CreateUser']) && $dbAllowReg == '1') {
			//Store post vars
			$NewUserName = $_POST['CreateUser'];
			$NewUserPW = $_POST['CreatePassword1'];
			$NewUserPWChk = $_POST['CreatePassword2'];
			//If there is no issue with the password complexity
			if($NewUserPW==$NewUserPWChk) {
				//if (!queryForUser($NewUserName) {
					//Unused username
					$salt = hash("md5",rand(1000000,9999999));
					$saltedpass = hash("sha256",$salt . $NewUserPW);
					insertNewUser($con,$NewUserName,$salt,$saltedpass);
					//If everything returned okay (add verification)
					$alert = "<p class='text-primary'>New user created!</p>";
				//}
			} else {
				$alert = "<p class='text-warning'>Your passwords did not match or did not meet complexity requirements!</p>";
			}
			
		}

		//But has made an attempt to login
		if (isset($_POST['InputUser'])) {
			//Capture post info
			$InputUser = $_POST['InputUser'];
			$InputPW = $_POST['InputPassword'];
			//Check database
			$loginResult = queryLogin($con,$InputUser,$InputPW);
			//If the database returns only one login
			if ($loginResult === false) {
				$alert = "<p class='text-warning'>Invalid login or password</p>";
			} else {
				//Database returned one result, start a session and assign session variables
				$loginResult = $loginResult->fetch_array();
				$_SESSION['user'] = $loginResult['UserName'];
				$_SESSION['UserID'] = $loginResult['UserID'];
				$_SESSION['Role'] = $loginResult['Role'];
				//Take user to index
				header('location: index.php');
			}
		}
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
	<?php buildNavBar(false, ''); ?>
    <div class="container-fluid">
      <div class="row">
		<?php buildSideBar("login");?>
			<div class="col-sm-9 col-sm-offset-3 col-md-4 col-md-offset-2 main">
				<h1 class="page-header">Log In</h1>
				<?=$alert?>
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="headingOne">
				      <h4 class="panel-title">
				        <a data-toggle="collapse" data-parent="#accordion" href="#collapseExisting" aria-expanded="true" aria-controls="collapseExisting">
				          Existing User
				        </a>
				      </h4>
				    </div>
				    <div id="collapseExisting" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
				      <div class="panel-body">
						<form method="post" class="form-group" role="form">
						  <div class="form-group">
							<label for="InputUser">Username</label>
							<input type="text" autofocus class="form-control" name="InputUser" id="InputUser" placeholder="Username">
						  </div>
						  <div class="form-group">
							<label for="InputPassword">Password</label>
							<input type="password" class="form-control" name="InputPassword" id="InputPassword">
						  </div>
						  <button type="submit" class="btn btn-primary" action="#">Log In</button>
						</form>
				      </div>
				    </div>
				  </div>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="headingTwo">
				      <h4 class="panel-title">
				        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseNew" aria-expanded="false" aria-controls="collapseNew">
				          Create New Account
				        </a>
				      </h4>
				    </div>
				    <div id="collapseNew" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
				      <div class="panel-body">
						<?php if ($dbAllowReg==1): ?>
								<form method="post" class="form-group" role="form">
								  <div class="form-group">
									<label for="CreateUser">Username</label>
									<input type="text" autofocus class="form-control" name="CreateUser" id="CreateUser" placeholder="Username">
								  </div>
								  <div class="form-group">
									<label for="CreatePassword1">Password</label>
									<input type="password" class="form-control" name="CreatePassword1" id="CreatePassword1" placeholder="Password">
								  </div>
								  <div class="form-group">
									<label for="CreatePassword2">Re-Type Password</label>
									<input type="password" class="form-control" name="CreatePassword2" id="CreatePassword2" placeholder="Confirm">
								  </div>
								  <button type="submit" class="btn btn-primary" action="#">Create User</button>
								</form>
						<?php else: ?>
							<p class="text-primary">The Admin has disabled user registration.</p>
						<?php endif; ?>
				      </div>
				    </div>
				  </div>
				</div>
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
	<?php
		$con->close();
	?>
	</body>
</html>
