<!DOCTYPE html>
<?php
session_start();
ini_set('display_errors', 1);
if ($_SESSION['logged_in_inventory_app_cs419'] == 0) {
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
<style type="text/css">
label {
float:left;
text-align:right;
display:block;
margin-right: 0.5em;
}

.optional{
visibility: collapse;
display: none;
}
<?php
if (empty($_POST["add_name"])) {
	echo '#add_confirmation{visibility:hidden; display:none;}\n';
} else {
	echo '#add_form{ visibility:hidden; display:none}\n';
}
?>
</style>
</head>
<body data-spy="scroll" data-target=".dropdown">
<div id="float_right" class="dropdown">
<a href="#" data-toggle="dropdown" onclick="showDropdown()">Hello, <?php echo $username ?><span class="caret"></span></a>
<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
<li class="divider"></li>
<li><a href="../logout.php">Logout</a></li>
<li><a href="../change_password.php">Change Password</a></li>
</ul>
</div>
<br><br>
<div class="container">
<div class="header">
<ul class="nav nav-pills pull-right">
<li><a href="admin.php">Item Lookup</a></li>
<li class="active"><a href="add.php">Add Item</a></li>
<li><a href="check.php">Check In/Out</a></li>
</ul>
<h3 class="text-muted">Inventory Locator</h3>
</div>

<div class="jumbotron">
<legend>Item to add to the Library</legend>

<form id="add_form" class="navbar-form" action="add.php" method="POST" enctype="multipart/form-data">
<table id="form_list">
	<tr>
		<td><label for='add_form'>Item Type</label></td>
		<td style="text-align:right"><select id='type' form="add_form" name="add_type" onchange="showOptions()">
			<option>Choose One...</option>
			<option value="computer">Computer</option>
			<option value="embedded">Embedded System / Microcontroller</option>
			<option value="book">Book</option>
			<option value="magazine">Magazine</option>
			<option value="tablet">Tablet</option>
			<option value="accessory">Electronic Accessory</option>
		</select></td>
	</tr>
	<tr>
		<td><label for="add_name">Item Name</label></td>
		<td style="text-align:right"><input id="add_name" name="add_name" type="text" placeholder="Item Name"></td>
	</tr>
	<tr>
		<td><label for="add_quantity">Quantity</label></td>
		<td style="text-align:right"><input id="add_quantity" name="add_quantity" type="text" placeholder="Item Quantity"></td>
	</tr>
	<tr class="optional" id="page_length">
		<td><label for="add_length">Page Length</label></td>
		<td style="text-align:right"><input id="add_length" name="add_length" type="text" placeholder="No. Pages"></td>
	</tr>
	<tr class="optional" id="ISBN">
		<td><label for="add_ISBN">ISBN/ISSN</label></td>
		<td style="text-align:right"><input id="add_ISBN" name="add_ISBN" type="text" placeholder="ISBN or ISSN"></td>
	</tr>
	<tr class="optional" id="OS">
		<td><label for="add_OS">OS</label></td>
		<td style="text-align:right"><input id="add_OS" name="add_OS" type="text" placeholder="Operating System"></td>
	</tr>
	<tr class="optional" id="manufacturer">
		<td><label for="add_man"> Manufacturer</label></td>
		<td style="text-align:right"><input id = "add_man" name="add_man" type="text" placeholder="Manufacturer"></td>
	</tr>
	<tr class="optional" id="accessories">
		<td><label for="add_accessories">Accessories</label></td>
		<td style="text-align:right"><textarea id="add_accessories" name="add_accessories" rows="2" form="add_form" placeholder="List all included accessories here."></textarea></td>
	</tr>
	<tr>
		<td><label for="add_condition">Condition</label></td>
		<td style="text-align:right"><select id="add_condition" form="add_form" name="add_condition">
			<option>Choose One...</option>
			<option value="New">New</option>
			<option value="Very Good">Very Good</option>
			<option value="Good">Good</option>
			<option value="Fair">Fair</option>
			<option value="Poor">Poor</option>
		</select></td>
	</tr>
	<tr>
		<td><label>Brief Description</label></td>
		<td style="text-align:right"><textarea name="add_description" rows="5" form="add_form" placeholder="Enter a description."></textarea> </td>
	</tr>
</table>
	<input type="submit" text="Submit">
</form>
<div id="add_confirmation">
<?php
                    if ((!empty($_POST["add_name"]))) {
                        if ($_SESSION['logged_in_inventory_app_cs419'] == 1) {
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
                                echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                            }

                            // Generic start to the query
                            $query = 'INSERT INTO Item (barcodeID, name, type_of_item, itemQuantity, num_available, description, item_condition';
                            $fail = False;

                            // Get values from user
                            $type = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_type", FILTER_SANITIZE_STRING));
                            $name = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_name", FILTER_SANITIZE_STRING));
                            $barcode = $type . $name . rand();
                            $description = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_description", FILTER_SANITIZE_STRING));
                            $condition = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_condition", FILTER_SANITIZE_STRING));
                            $quantity = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_quantity", FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))));

                            // use these in cases of a book / computer
                            if ((strcmp($type, "book") == 0) || (strcmp($type, "magazine") == 0)) {
                                $length = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_length", FILTER_VALIDATE_INT, array("options" => array("min_range" => 0))));
                                $ISBN = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_ISBN", FILTER_SANITIZE_STRING));
                                $query = $query . ", pages_if_book, ISBN_or_ISSN) VALUES ('" . $barcode . "','" . $name . "','" . $type . "','" . $quantity . "', '" . $quantity . "', '"
                                        . $description . "','" . $condition . "', '" . $length . "' ,'" . $ISBN . "');";
                            } else if ((strcmp($type, "computer") == 0) || (strcmp($type, "tablet") == 0)) {
                                $accessories = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_accessories", FILTER_SANITIZE_STRING));
                                $OS = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_OS", FILTER_SANITIZE_STRING));
                                $man = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_man", FILTER_SANITIZE_STRING));
                                $query = $query . ", OS_if_computer, hardware_man, accessories) VALUES ('" . $barcode . "','" . $name . "','" .
                                        $type . "','" . $quantity . "', '" . $quantity . "', '" . $description . "','" . $condition .
                                        "','" . $OS . "', '" . $man . "', '" . $accessories . "');";
                            } else if (strcmp($type, "accessory") == 0 || strcmp($type, "embedded") == 0) {
                                $accessories = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_accessories", FILTER_SANITIZE_STRING));
                                $query = $query . ", accessories) VALUES ('" . $barcode . "','" . $name . "','" .
                                        $type . "','" . $quantity . "', '" . $quantity . "', '" . $description . "','" . $condition . "','" . $accessories . "');";
                            } else {
                                $fail = True;
                                echo "Bad type, please try again<br>\n";
                            }

                            $barcodeSite = "http://chart.googleapis.com/chart?chs=200x200&cht=qr&choe=UTF-8&chld=H&chl=";
                            $barcodeSite = $barcodeSite . $barcode;

                            //echo "Query: " . $query . "<br>";
                            if (!$fail) {
                                // Pass the query to add the user
                                if (!mysqli_query($mysqli, $query)) {
                                    echo "Add failed: " . $query . "<br>\n";
                                } else {
                                    // Tell the user of the success
                                    echo "<h4>Item Added!</h4><br>\n";
                                    echo "<table>\n";
                                    echo "<tr><th>Type</th> <td>" . $type . "</td></tr>\n";
                                    echo "<tr><th>Name</th> <td>" . $name . "</td></tr>\n";
                                    if (strcmp($type, "book") == 0) {
                                        echo "<tr><th>Book length</th> <td>" . $length . "</td></tr>\n";
                                        echo "<tr><th>ISBN</th> <td>" . $ISBN . "</td></tr>\n";
                                    } else {
                                        echo "<tr><th>Accessories</th> <td>" . $accessories . "</td></tr>\n";
                                        if (strcmp($type, "computer") == 0 || (strcmp($type, "tablet") == 0)) {
                                            echo "<tr><th>Operating System</th> <td>" . $OS . "</td></tr>\n";
                                            echo "<tr><th>Manufacturer</th> <td>" . $man . "</td></tr>\n";
                                        }
                                    }
                                    echo "<tr><th>Condition</th> <td>" . $condition . "</td></tr>\n";
                                    echo "<tr><th>Description</th> <td>" . $description . "</td></tr>\n";
                                    echo "<tr><th>QR Code</th>\n";
                                    echo "<td><img src=\"" . $barcodeSite . "\" alt=\"Barcode\"></td></tr>\n";
                                    echo "</table>\n";
                                }
                            }
                        }
                    }
                    ?>

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
	function showOptions() {
		var type_choice = document.getElementById("type").value;
		$("#OS").addClass("optional");
		$("#accessories").addClass("optional");
		$("#accessories_box").addClass("optional");
		$("#manufacturer").addClass("optional");
		$("#page_length").addClass("optional");
		$("#ISBN").addClass("optional");
		switch (type_choice) {
			case "computer":
			case "tablet":
				$("#OS").removeClass("optional");
				$("#accessories").removeClass("optional");
				$("#accessories_box").removeClass("optional");
				$("#manufacturer").removeClass("optional");
				break;
			case "book":
			case "magazine":
				$("#page_length").removeClass("optional");
				$("#ISBN").removeClass("optional");
				break;
		}
	}
</script>
</html>