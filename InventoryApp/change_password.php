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
	$username = $_SESSION['username'];
	
	// Check if user entered name and password
	if ((!empty($_POST["current_username"])) && (!empty($_POST["old_password"])) && (!empty($_POST["new_password"]))) {	
		// Store post values
		$name = $_POST['current_username'];
		$old_password = $_POST['old_password'];
		$password = $_POST['new_password'];
		$password2 = $_POST['password_verify'];
		echo "" . $name;
		echo "" . $old_password;
		echo "" . $password;
		echo "" . $password2;
		
		if ($password != $password2) {
			$myerrno = 5;
			//echo "It says the passwords are NOT identical.<br>";
		} else {
			$mysuccessno = 5;
			echo "Passwords match.<br>";
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
				echo "Executed username select successfully.<br>";
				$mysuccessno = 2;
				$stmt->store_result();
				$rowcount = $stmt->num_rows;
				//echo "Row count: " . $rowcount . "<br>";
				$stmt->close();
				if ($rowcount == 0)  { 
					//if username already exists, ask them to try again with another
					$myerrno = 6;
					//echo "Username does not exist.";
				} else {
					// Prepare update
					if (!($stmt = $mysqli->prepare("UPDATE SiteUsers SET password = '$password' WHERE username='$name'"))) {
						//echo "Prepare to insert failed: "  . $stmt->errno . " " . $stmt->error;
						$myerrno = 1;
					} else {
						$mysuccessno = 1;
						//echo "Prepared username select successfully.<br>";
						//Execute insert
						if (!$stmt->execute()) {
							//echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
							$myerrno = 2;
						} else {
							$mysuccessno = 6;
							//echo "Execute update row successfully.<br>";
						}		
					}
					$stmt->close();
				}
			}
		}
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
    <title>Change Password</title>
    <link href="../dist/css/bootstrap.css" rel="stylesheet">
    <link href="signin.css" rel="stylesheet">
	<script src="jquery-1.10.2.js"></script>
	<script src="jquery.validate.min.js"></script>
	<script src="../dist/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#message").hide();
			$("#message_success").hide();
			
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
					$("#message_success").text("You password has been updated.");
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
				case 6:
					$("#message").html("Username does not exist. Please <a href='create_account.php'>create an account</a> or try again.");
					$("#message").show();
					break;
				default: 
					$("#message").hide();
			}
		}
	</script>
  </head>
  <body data-spy="scroll" data-target=".dropdown">
	<div id="float_right" class="dropdown">
		<a href="#" data-toggle="dropdown" onclick="showDropdown()">Hello, <?php echo $username ?><span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			<li class="divider"></li>
			<li><a href="../logout.php">Logout</a></li>
		</ul>
	</div>
    <div class="container">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="index.php">Item Lookup</a></li>
			</ul>
			<h3 class="text-muted">Inventory Locator</h3>
		</div>
		<form class="form-signin" action="change_password.php" method = "POST" enctype="multipart/form-data">
			<h2 class="form-signin-heading">Change Password</h2>
			<input name="current_username" type="text" class="form-control" placeholder="Username" required autofocus>
			<input name="old_password" type="password" class="form-control" placeholder="Old Password" required>
			<input name="new_password" type="password" class="form-control" placeholder="New Password" required>
			<input name="password_verify" type="password" class="form-control" placeholder="Re-enter New Password" required>
			<label class="checkbox">
			  <input type="checkbox" value="remember-me"> Remember me
			</label>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Change Password</button>
		</form>
		<div id="message" style="margin-left:50px"></div><br>
		<div id = "message_success" style="margin-left:50px"></div>
    </div>
  </body>
</html>
