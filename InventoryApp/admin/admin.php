<!DOCTYPE html>
<?php 
session_start();
ini_set('display_errors',1); 
error_reporting(E_ALL);
if ($_SESSION['logged_in']==0) {
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../index.php">';    
    exit;
} else {
	$username = $_SESSION['username'];
}
?>

<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Inventory Locator</title>
		<script src="../jquery-1.10.2.js"></script>
		<script src="../../dist/js/bootstrap.min.js"></script>
		<link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
		<link href="../../dist/css/bootstrap.css" rel="stylesheet">
		<link href="jumbotron-narrow.css" rel="stylesheet">
		<script type="text/javascript">
			function showDropdown() {
				$('.dropdown-toggle').dropdown();
			}
		</script>
	</head>
	<body data-spy="scroll" data-target=".dropdown">
		<div id="float_right" class="dropdown">
			<a href="#" data-toggle="dropdown" onclick="showDropdown()">Hello, <?php echo $username ?><span class="caret"></span></a>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
				<li class="divider"></li>
				<li><a href="../logout.php">Logout</a></li>
				<li><a href="change_password.php">Change Password</a></li>
			</ul>
		</div>
		<br><br>
		<div class="container">
			<div class="header">
				<ul class="nav nav-pills pull-right">
					<li class="active"><a href="#">Item Lookup</a></li>
					<li><a href="add.html">Add Item</a></li>
					<li><a href="check.html">Check In/Out</a></li>
				</ul>
				<h3 class="text-muted">Inventory Locator</h3>
			</div>

			<div class="jumbotron">
				<form class="navbar-form" action="admin.php" method="POST" enctype="multipart/form-data">
					<input id="search_bar" name="search_query" type="text" placeholder="Search">
					<button id="search_button" class="btn btn-success" type="submit"><img src="search_bar.jpg"></button>
				</form>
				<div id="search_results_div" class="jumbotron">
					<?php
						if ($_SESSION['logged_in']==1) {
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

							if ((!empty($_POST["search_query"]))) {	
								// Store post values
								$search_query = $_POST['search_query'];
								//echo "" . $search_query;
								
								// Prepare select
								if (!($stmt = $mysqli->prepare("SELECT barcodeID, name FROM Item WHERE name LIKE '%$search_query%'"))) {
									//echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
									$myerrno = 1;
								} else {
									$mysuccessno = 1;
								}
								// Execute select
								if (!$stmt->execute()) {
									//echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
									$myerrno = 2;
								} else {
									// execute was successful
									$mysuccessno = 2;
									$stmt->bind_result($barcodeID, $itemname);
									echo "<form id='item_detail_forward' action='item_details.php' method='POST' enctype='multipart/form-data'><table><tr><td style='text-decoration: underline; width: 50%'>barcodeID</td><td style='text-decoration: underline; width: 50%'>name</td></tr>\n";
									while ($stmt->fetch()) {
										echo "<tr><td><button class='barcodeButton' type='submit'><a name='barcodeID'>" . $barcodeID . "</a></button></td><td>" . $itemname . "</td></tr>\n";
									}
									echo "</table></form>";
									$stmt->close();
								}
							}
						}
					?>
					<br><br><br><br><br><br><br>
				</div>
			</div>
			<div id="message" style="margin-left:50px"></div><br>
			<div id = "message_success" style="margin-left:50px"></div>
			<div class="row marketing">
				<div class="col-lg-6">
				  <h4>Request an Item</h4>
				  <p>Send us a request to add a new item to our inventory.</p>
				</div>

				<div class="col-lg-6">
				  <h4>Contact Us</h4>
				  <p>Phone: 123-123-1234.</p>
				</div>
			</div>

			<div class="footer">
				<p>&copy; 2013</p>
			</div>
		</div>
	</body>
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
</html>
