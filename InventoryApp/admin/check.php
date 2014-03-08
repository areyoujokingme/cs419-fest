<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
$flag = false;
if (!isset($_SESSION['logged_in_inventory_app_cs419'])) {
    $flag = true;
} elseif ($_SESSION['logged_in_inventory_app_cs419'] == 0) {
    $flag = true;
}
if ($flag) {
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../index.php">';
    exit;
} else {
    $username = $_SESSION['username'];
}
?>
<!DOCTYPE html>
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
                <li><a href="../change_password.php">Change Password</a></li>
            </ul>
        </div>
        <br><br>
        <div class="container">
            <div class="header">
                <ul class="nav nav-pills pull-right">
                    <li class="active"><a href="#">Item Lookup</a></li>
                </ul>
                <h3 class="text-muted">Inventory Locator</h3>
            </div>


            <div class="jumbotron">
                <form class="navbar-form" action="check.php" method="GET" enctype="multipart/form-data">
                    <input id="search_bar" name="search_query" type="text" placeholder="Search">
                    <button id="search_button" class="btn btn-success" type="submit"><img src="search_bar.jpg"></button>
                </form>
                <div id="search_results_div" class="jumbotron">
                    <?php
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
                            $myerrno = 0;
                        } else {
                            //echo "Connected to database successfully.<br>";
                        }


                        if ((!empty($_GET["search_query"]))) {
                            // Store post values
                            $search_query = filter_input(INPUT_GET, 'search_query', FILTER_SANITIZE_SPECIAL_CHARS);
                            //echo "" . $search_query;
                            // Prepare select
                            if (!($stmt = $mysqli->prepare("SELECT barcodeID, name,num_available FROM Item WHERE name LIKE '%$search_query%'"))) {
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


                                //***************************Changes made by Arif *****************//
                                //*************************** Changes made start here *************//
                                // execute was successful
                                $mysuccessno = 2;
                                $stmt->bind_result($barcodeID, $itemname, $quantity);
                                $stmt->store_result();
                                echo "<table>
                                    <tr>
                                        <td style='text-decoration: underline; width: 30%'>Barcode</td>
                                        <td style='text-decoration: underline; width: 40%'>Name</td>
                                        <td style='text-decoration: underline; width: 20%'>Quantity</td>
                                        <td style='text-decoration: underline; width: 10%'></td>
                                    </tr>\n";
                                while ($stmt->fetch()) {


                                    if ($quantity > 0) {


                                        $isItemCheckedIn = isCheckedInOrOut($username, $barcodeID, $mysqli);


                                        echo "<tr>
                                                        <td>
                                                            <button class='barcodeButton' name='barcodeID' onClick=\"window.location='item_details.php?barcodeID=$barcodeID'\" >
                                                                " . $barcodeID . "</button>
                                                        </td>
                                                        <td>". $itemname . "</td>
                                                        <td>" . $quantity . "</td>
                                                        <td>";


                                        if (($isItemCheckedIn == 2) || ($isItemCheckedIn == 0)) {
                                            // The item has either been check in or hasn't been checked out yet
                                            // Allow the item to be checked out.


                                            echo "<form id='item_sign_in_out' action='../user/item_check_in_out.php' method='POST'>
                                                       <table>
				<tr>
					<input type='hidden' name='action' value='check_out'>
					<input type='hidden' name='barcodeID' value=$barcodeID>
					<input type='hidden' name='availableNum' value=$quantity>
					<button class='barcodeButton' name='check out' value= type='submit'><a>Check Out</a></button>
				</tr>
                                                      </table>
                                            </form>";
                                        } else {
                                            // The item has either been checked out
                                            // Allow the item to be checked in.
                                            echo "<form id='item_sign_in_out' action='../user/item_check_in_out.php' method='POST'>
				<table>
					<tr>
						<input type='hidden' name='action' value='check_in'>
						<input type='hidden' name='barcodeID' value=$barcodeID>
						<input type='hidden' name='availableNum' value=$quantity>
						<button class='barcodeButton' name='check out' value= type='submit'><a>Check In</a></button>
					</tr>
				</table>
			</form>";
                                        }
                                        echo "</td>
												</tr></td>";
                                    } else {
                                        echo "<tr><td><button class='barcodeButton' name='barcodeID' value=$barcodeID type='submit'><a>" . $barcodeID . "</a></button></td><td>"
                                        . $itemname . "</td><td>" . $quantity . "</td> <td> N/A</td></tr>";
                                    }
                                }
                                echo "</table></form>";


                                //***************************Changes made by Arif *****************//
                                //*************************** Changes end here *************//
                                $stmt->close();
                            }
                        }
                    }

                    function isCheckedInOrOut($username, $barcodeID, $mysqli) {


                        $checkIn = "";
                        $checkOut = "";
                        $result = 0;




                        $userID = "";
                        /*                         * ** Retrieving the  userID.* */


                        //prepare select
                        if (!($stmt2 = $mysqli->prepare("SELECT userID FROM SiteUsers WHERE username='$username'"))) {
                            $myerrno = 1;
                        } else {
                            $mysuccessno = 1;
                        }
                        // Execute select
                        if (!$stmt2->execute()) {
                            //echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                            $myerrno = 2;
                        } else {
                            // execute was successful						
                            $mysuccessno = 2;
                            $stmt2->bind_result($userID);
                        }


                        $stmt2->close();
                        /*                         * ** Retrieving the  userID ends* */


                        /*                         * * Retrieving check in and check out record for this item * */


                        // Prepare select
                        if (!($stmt3 = $mysqli->prepare("SELECT checkIN,checkOUT FROM Transaction WHERE userID='$userID' AND barcodeID='$barcodeID' "))) {
                            //echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                            $myerrno = 1;
                        } else {
                            $mysuccessno = 1;
                        }
                        // Execute select
                        if (!$stmt3->execute()) {
                            //echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                            $myerrno = 2;
                        } else {
                            $stmt3->bind_result($checkIn, $checkOut);
                        }
                        $stmt3->store_result();


                        if ($stmt3->num_rows > 0) {


                            $stmt3->fetch();


                            if (strcmp($checkIn, "0000-00-00 00:00:00") == 0) {
                                $result = 1;  // The item has only been checked out
                            } else {
                                $result = 2; // The item has been checked out and checked in
                            }
                        } else {
                            $result = 0;  // The item has not been checked out yet.									;
                        }
                        $stmt3->close();
                        return $result;
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
            var myerrnumba = <?php echo $myerrno; ?>;
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

