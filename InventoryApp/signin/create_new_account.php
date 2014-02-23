<!DOCTYPE html>
<?php
session_start();
	//first we have to connect to MYSQL
	ini_set('display_errors', '1');
	$dbhost = 'oniddb.cws.oregonstate.edu';
	$dbname = 'ashmorel-db';
	$dbuser = 'ashmorel-db';
	$dbpass = 'BL1p3hMvNVjhUDO8';
	$myerrno = -1;
	$mysuccessno = -1;
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($mysqli->connect_errno) {
		$myerrno = 0;
	} else {
		//echo "Connected to database successfully.<br>";
	}
	
	// Check if user entered name and password
	if ((!empty($_POST["create_username"])) && (!empty($_POST["create_password"]))) {	
		// Store post values
		$name = $_POST['create_username'];
		$password = $_POST['create_password'];
		$password2 = $_POST['password_verify'];
		
		if ($password != $password2) {
			$myerrno = 5;
			//echo "It says the passwords are NOT identical.<br>";
		} else {
			$mysuccessno = 5;
			//echo "Passwords match.<br>";
			//check to see if username already exists
			// Prepare select
			if (!($stmt = $mysqli->prepare("SELECT username FROM SiteUsers WHERE username='$name'"))) {
				//echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				$myerrno = 1;
			} else {
				$mysuccessno = 1;
				//echo "Prepared username select successfully.<br>";
			}
			// Execute select
			if (!$stmt->execute()) {
				//echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
				$myerrno = 2;
			} else {
				// execute was successful
				//echo "Executed username select successfully.<br>";
				$mysuccessno = 2;
				$stmt->store_result();
				$rowcount = $stmt->num_rows;
				//echo "Row count: " . $rowcount . "<br>";
				$stmt->close();
				if ($rowcount > 0)  { 
					//if username already exists, ask them to try again with another
					$myerrno = 3;
					//echo "Username already exists.";
				} else {
					//username hasn't already been chosen. Add username and password to database and let them login
					// Prepare insert
					if (!($stmt = $mysqli->prepare("INSERT INTO `SiteUsers`(`username`,`password`) VALUES(?,?)"))) {
						//echo "Prepare to insert failed: "  . $stmt->errno . " " . $stmt->error;
						$myerrno = 1;
					} else {
						$mysuccessno = 1;
						//echo "Prepared username select successfully.<br>";
						//Bind parameters
						$hashed_password = crypt($password, CRYPT_SHA256);
						if (!$stmt->bind_param("ss", $name, $hashed_password)) {
							//echo "Bind failed: " . $stmt->errno . " " . $stmt->error;
							$myerrno = 4;
						} else {
							//bind passed 
							$mysuccessno = 4;
							//echo "Bound to strings successfully.<br>";
							//Execute insert
							if (!$stmt->execute()) {
								//echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
								$myerrno = 2;
							} else {
								$mysuccessno = 6;
								//echo "Execute insert row successfully.<br>";
							}		
						}
					}
					$stmt->close();
				}
			}
		}
	}
	if (isset($_SESSION['username']) && ($_SESSION['logged_in']==1)) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=sign_in.php">';    
		exit;
	}
	//echo '<pre>' . htmlspecialchars(print_r(get_defined_vars(), true));
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <title>Create New Account</title>
    <link href="../dist/css/bootstrap.css" rel="stylesheet">
    <link href="signin.css" rel="stylesheet">
	<script src="jquery-1.10.2.js"></script>
	<script src="jquery.validate.min.js"></script>
	<script src="../dist/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#message").hide();
			$("#message_success").hide();
			$("#create_account").validate({
				rules: {
					create_username: {
						required: true,
						email: true
					},
					create_password: {
						required: true,
						minlength: 6,
						maxlength: 8
					},
					password_verify: {
						required: true,
						equalTo: "#create_password"
					}
				}
			});
			//print success and error messages, for feedback and debugging.
			var mysuccessnumba = <?php echo $mysuccessno; ?>;
			success(mysuccessnumba);	
			var myerrnumba =  <?php echo $myerrno; ?>;
			error(myerrnumba);
		});
		
		function success(mysuccessnumba) {
			switch (mysuccessnumba) {
				case 0:
					$("#message_success").text("Connected to the database successfully.");
					$("#message_success").show();
					break;
				case 1: 
					$("#message_success").text("Prepared statement successfully.");
					$("#message_success").show();
					break;
				case 2:
					$("#message_success").text("Executed statement successfully.");
					$("#message_success").show();
					break;
				case 3:
					$("#message_success").text("Username found.");
					$("#message_success").show();
					break;
				case 4:
					$("#message_success").text("Bind successful.");
					$("#message_success").show();
					break;
				case 5:
					$("#message_success").text("Passwords match.");
					$("#message_success").show();
					break;
				case 6:
					$("#message_success").text("Account created. Now you can login to the site.");
					$("#message_success").show();
					break;
				default: 
					$("#message_success").hide();
			}
		}
		function error(myerrno) {
			switch (myerrno) {
				case 0:
					$("#message").text("Failed to connect to database.");
					$("#message").show();
					break;
				case 1:
					$("#message").text("Prepare statement failed.");
					$("#message").show();
					break;
				case 2:
					$("#message").text("Execute statement failed.");
					$("#message").show();
					break;
				case 3:
					$("#message").html("Username already exists. Please <a href='create_account.php'>try again</a> with a different username.");
					$("#message").show();
					break;
				case 4:
					$("#message").text("Bind failed.");
					$("#message").show();
					break;
				case 5:
					$("#message").html("Passwords do not match. Please <a href='create_account.php'>try again.</a>");
					$("#message").show();
					break;
				default: 
					$("#message").hide();
			}
		}
	</script>
  </head>
  <body>
    <div class="container">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="sign_in.php">Sign In</a></li>
				<li class="active"><a href="#">Create Account</a></li>
			</ul>
			<h3 class="text-muted">Inventory Locator</h3>
		</div>			
		<form id="create_account" class="form-signin" action="create_new_account.php" method = "POST" enctype="multipart/form-data">
			<h2 class="form-signin-heading">Create Account</h2>
			<input  id="create_username" name="create_username" type="email" class="form-control" placeholder="Email address" required autofocus>
			<input id="create_password" name="create_password" type="password" class="form-control" placeholder="Password" required>
			<input  id="password_verify" name="password_verify" type="password" class="form-control" placeholder="Re-enter Password" required>
			<label class="checkbox">
			  <input type="checkbox" value="remember-me"> Remember me
			</label>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Create Account</button>
		</form>
		<div id="message" style="margin-left:50px"></div><br>
		<div id = "message_success" style="margin-left:50px"></div>
    </div>
  </body>
</html>
