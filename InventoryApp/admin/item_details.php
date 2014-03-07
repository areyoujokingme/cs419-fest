<!DOCTYPE html>
<?php 
session_start();
ini_set('display_errors',1); 
$flag=false;
if (!isset($_SESSION['logged_in_inventory_app_cs419'])) {
	$flag=true;	
}
elseif ($_SESSION['logged_in_inventory_app_cs419'] == 0) {
	$flag=true;
}
if($flag){
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
					<li><a href="admin.php">New Search</a></li>
					<li class="active"><a href="#">Item Details</a></li>
					<li><a href="add.php">Add Item</a></li>
					<li><a href="check.php">Check In/Out</a></li>
				</ul>
				<h3 class="text-muted">Inventory Locator</h3>
			</div>

			<div class="jumbotron">
				<h2> Item Details</h2>
				<div id="search_results_div" class="jumbotron">
					<?php
						if ($_SESSION['logged_in_inventory_app_cs419']==1) {
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

							if ((!empty($_GET["barcodeID"]))) {	
								// Store post values
								$barcodeID = $_GET['barcodeID'];
								//echo "barcodeID: " . $barcodeID . "<br>";
								// Prepare select
								if (!($stmt = $mysqli->prepare("SELECT checkIN, checkOUT FROM Transaction WHERE barcodeID='$barcodeID'"))) {
									//echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
									$myerrno = 1;
								} else {
									$mysuccessno = 1;
								}
								// Execute select
								if (!$stmt->execute()) {
									echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
									$myerrno = 2;
								} else {
									// execute was successful
									$mysuccessno = 2;
									$stmt->bind_result($checkIN, $checkOUT);
									//echo "IN: " . $checkIN . " OUT: " . $checkOUT;
								}
								$stmt->close();
								// Prepare select
								$query = "SELECT barcodeID, name, description, accessories, type_of_item, itemQuantity, num_available, item_condition, ISBN_or_ISSN, pages_if_book, OS_if_computer, hardware_man FROM Item WHERE barcodeID='" . $barcodeID . "' AND quantity_index=1";
								if (!($stmt = $mysqli->prepare($query))) {
									//echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
									$myerrno = 1;
								} else {
									$mysuccessno = 1;
								}
								// Execute select
								if (!$stmt->execute()) {
									echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
									$myerrno = 2;
								} else {
									// execute was successful
									$mysuccessno = 2;
									$stmt->bind_result($barcodeID, $itemname, $description, $accessories, $type, $total, $available, $condition, $isbn, $pages, $OS, $manufacturer);
									//echo $type;
									echo "
										<table>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Barcode ID:</td><td><input style='width:400px' value='" . $barcodeID . "'readonly /></td></tr>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Item Name:</td><td><input style='width:400px' value='" . $itemname . "' readonly /><td></tr>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Number Available:</td><td><input style='width:400px' value='" . $available . "' readonly /></tr>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Item Quantity:</td><td><input style='width:400px' value='" . $total . "' readonly /></tr>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Description:</td><td><input style='width:400px' value='" . $description . "' readonly /></tr>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Check In:</td><td><input style='width:400px' value='" . $checkIN . "' readonly /></tr>
											<tr style='font-size: 75%; text-decoration: underline;'><td>Check Out:</td><td><input style='width:400px' value='" . $checkOUT . "' readonly /></tr>";
											if ($type == 'computer' || $type == 'tablet') {
												echo "<tr style='font-size: 75%; text-decoration: underline;'><td>Operating System:</td><td><input style='width:400px' type='text' value='" . $OS . "' readonly /></tr>
												<tr style='font-size: 75%; text-decoration: underline;'><td>Hardware Manufacturer:</td><td><input style='width:400px'  value='" . $manufacturer . "' readonly /></tr>";													
											}
											if ($type == 'book' || $type == 'magazine') {
												echo "<tr style='font-size: 75%; text-decoration: underline;'><td>No. Pages:</td><td><input style='width:400px'  value='" . $pages . "' readonly /></tr>												
												<tr style='font-size: 75%; text-decoration: underline;'><td>ISBN/ISSN:</td><td><input style='width:400px' value='" . $isbn . "' readonly /></tr>";
											}
											echo "<tr style='font-size: 75%; text-decoration: underline;'><td>Item Condition:</td><td><input style='width:400px' value='" . $condition . "' readonly /></tr>";
											if ($accessories != null) {
												echo "<tr style='font-size: 75%; text-decoration: underline;'><td>Accessories:</td><td><input style='width:400px' value='" . $accessories . "' readonly /></tr>";												
											}
											echo "</table>";
									$stmt->close();
								}
							}
						}
					?>
				</div>
			</div>
			<?php echo "barcodeID: " . $barcodeID . 
				"<br>name: " . $itemname . 
				"<br>available (out of): " . $available . 
				"(" . $total . 
				") <br>description: " . $description . 
				"<br>checkIN/OUT: " . $checkIN .
				"/" . $checkOUT . 
				"<br>condition: " . $condition . "";
			?>
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
