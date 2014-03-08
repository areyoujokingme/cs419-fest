<!DOCTYPE html>
<?php 
session_start();
ini_set('display_errors',1); 
if ($_SESSION['logged_in_inventory_app_cs419']==0) {
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php">';    
    exit;
} else {
	//first we have to connect to MYSQL
	ini_set('display_errors', '1');
	$dbhost = 'mysql.cs.orst.edu';
	$dbname = 'cs419_group1';
	$dbuser = 'cs419_group1';
	$dbpass = 'JvqM38DV4PsH7cyH';
	$myerrno = -1;
	$mysuccessno = -1;
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($mysqli->connect_errno) {
		$myerrno = 0;
	} else {
		//echo "Connected to database successfully.<br>";
	}
	$username = $_SESSION['username'];
	if ((!empty($_POST["send_email"]))) {
		$emailed_code = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",7)),7,7);
		$subject = "Change Password Request: Your verification code.";
		$body = "<html>
				<head>
				  <title>Test Mail</title>
				</head>
				<body>Your verification code is " . $emailed_code . 
				". Please enter this code in the appropriate field 
				<a href='http://web.engr.oregonstate.edu/~ashmorel/419/InventoryApp/change_password.php'>here</a>.
				</body>
				</html>";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Noreply <noreply@example.com>' . "\r\n";
		if (!mail($username, $subject, $body, $headers)) {
			echo "Email failed to send.";
		}
	}
	// Check if user entered name and password
	if ((!empty($_POST["current_username"])) && (!empty($_POST["verification_code"])) && (!empty($_POST["new_password"]))) {	
		// Store post values
		$name = $_POST['current_username'];
		$verification_code = $_POST['verification_code'];
		$password = $_POST['new_password'];
		$password2 = $_POST['password_verify'];
		//echo "" . $name;
		//echo "" . $verification_code;
		//echo "" . $password;
		//echo "" . $password2;
		
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
}
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
			$("#hideForm").hide();
			$("#instructions").text("In order to change your password, please enter the verification number which been emailed to you at <?php echo $username ?> along with your new password in the fields above.");
			document.getElementById("verification_code").onclick = function() {
				$("#hideForm").show();
				$("#change_password").validate({
					rules: {
						verification_code: {
							required: true,
							minlength: 7,
							maxlength: 7
						},
						new_password: {
							required: true,
							minlength: 6,
							maxlength: 8
						},
						password_verify: {
							required: true,
							equalTo: "#new_password"
						}
					}
				});
			}
			//print success and error messages, for feedback and debugging.
			var mysuccessnumba = <?php echo $mysuccessno; ?>;
			success(mysuccessnumba);	
			var myerrnumba =  <?php echo $myerrno; ?>;
			error(myerrnumba);
		});
</script>
</head>
<body data-spy="scroll" data-target=".dropdown">
	<div id="float_right" class="dropdown">
		<a href="#" data-toggle="dropdown" onclick="showDropdown()">Hello, <?php echo $username ?><span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			<li class="divider"></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	</div>
	<br><br>
	<div class="container">
		<div class="header">
			<ul class="nav nav-pills pull-right">
				<li><a href="index.php">Item Lookup</a></li>
			</ul>
			<h3 class="text-muted">Inventory Locator</h3>
		</div>
		<form id="change_password" class="form-signin" action="change_password.php" method = "POST" enctype="multipart/form-data">
			<button class="btn btn-lg btn-primary btn-block" type="submit" id="verification_code" name="send_email" value="yes">Get Email Verification Code</button>
			<div id="hideForm">
				<h2 class="form-signin-heading">Change Password</h2>
				<input id="current_username" name="current_username" type="text" class="form-control" placeholder="Email Address" value="<?php echo $username?>" readonly >
				<input id="verification_code" name="verification_code" class="form-control" placeholder="Verification Code" required autofocus>
				<input id="new_password" name="new_password" type="password" class="form-control" placeholder="New Password" required>
				<input id="password_verify" name="password_verify" type="password" class="form-control" placeholder="Re-enter New Password" required>
				<label class="checkbox">
				  <input type="checkbox" value="remember-me"> Remember me
				</label>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Change Password</button>
			</div>
		</form>
		<div id="instructions" style="margin-left:50px"></div><br>
		<div id="message" style="margin-left:50px"></div><br>
		<div id="message_success" style="margin-left:50px"></div>
	</div>
</body>
<script type="text/javascript">	
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
</html>
