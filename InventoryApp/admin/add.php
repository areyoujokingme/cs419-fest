<!DOCTYPE html>
<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$_SESSION['logged_in'] = 1;
$_SESSION['username'] = 'Arthur';

if ($_SESSION['logged_in'] == 0) {
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
        <title>Inventory Locator 2</title>
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
            li {
                list-style-type: none;
                padding: 10px;
                margin: 10px;
            }
            dt {
                float: left;
                clear: left;
                width: 130px;
                text-align: right;
                font-weight: bold;
                color: green;
            }
            dt:after {
                content: ":";
            }
            dd {
                text-align: left;
                margin: 0 0 0 150px;
                padding: 0 0 30px 0;
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
                    <li class="active"><a href="#">Add Item</a></li>
                    <li><a href="check.php">Check In/Out</a></li>
                </ul>
                <h3 class="text-muted">Add Inventory</h3>
            </div>

            <div class="jumbotron">
                <form id="add_form" class="navbar-form" action="add.php" method="POST" enctype="multipart/form-data">
                    <input name="add_barcode" type="hidden" value=<?php echo '"test1' . rand() . '"' ?>>
                    <table id="form_list">
                        <tr>
                            <th>Item Type</th>
                            <td>
                                <select form="add_form" name="add_type" onchange="showOptions()" id="type">
                                    <option></option>
                                    <option value="computer">Computer</option>
                                    <option value="embedded">Embedded System / Microcontroller</option>
                                    <option value="book">Book</option>
                                    <option value="magazine">Magazine</option>
                                    <option value="tablet">Tablet</option>
                                    <option value="accessory">Electronic Accessory</option>
                                </select> 
                            </td>
                        </tr>
                        <tr>
                            <th>Item Name</th>
                            <td><input name="add_name" type="text" placeholder="Name"> </td>
                        </tr>
                        <tr>
                            <th> Quantity</th>
                            <td><input name="add_quantity" type="text"></td>
                        </tr>
                        <tr class="optional" id="page_length">
                            <th>Page Length</th>
                            <td><input name="add_length" type="text"></td>
                        </tr>
                        <tr class="optional" id="ISBN">
                            <th>ISBN/ISSN</th>
                            <td><input name="add_ISBN" type="text" placeholder="ISBN or ISSN"></td>
                        </tr>
                        <tr class="optional" id="OS">
                            <th>Operating System</th>
                            <td><input name="add_OS" type="text"></td>
                        </tr>
                        <tr class="optional" id="manufacturer">
                            <th> Manufacturer</th>
                            <td><input name="add_man" type="text"></td>
                        </tr>
                        <tr class="optional" id="accessories">
                            <th>Additional Accessories</th>
                            <td><textarea name="add_accessories" rows="2" form="add_form"></textarea></td>
                        </tr>
                        <tr>
                            <th>Condition</th>
                            <td>    
                                <select form="add_form" name="add_condition">
                                    <option></option>
                                    <option value="new">New</option>
                                    <option value="very_good">Very Good</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Brief Description</th>
                            <td><textarea name="add_description" rows="5" form="add_form"></textarea> </td>
                        </tr>
                        <tr>
                            <th colspan="2"><input type="submit" text="Submit"></th>
                        </tr>
                    </table>
                </form>
                <div  id="add_confirmation">
                    <?php
                    if ((!empty($_POST["add_name"]))) {
                        if ($_SESSION['logged_in'] == 1) {
                            //first we have to connect to MYSQL
                            ini_set('display_errors', '1');

                            $dbhost = 'oniddb.cws.oregonstate.edu';
                            $dbname = 'vaseka-db';
                            $dbuser = 'vaseka-db';
                            $dbpass = 'owTjAABqZM6dPW93';

                            $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

                            if ($mysqli->connect_errno) {
                                echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                            }

                            // Generic start to the query
                            $query = 'INSERT INTO Item (barcodeID, name, type_of_item, itemQuantity, num_available, description, item_condition';
                            $fail = False;

                            // Get values from user
                            $barcode = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_barcode", FILTER_SANITIZE_STRING));
                            $type = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_type", FILTER_SANITIZE_STRING));
                            $name = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_name", FILTER_SANITIZE_STRING));
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

                            echo "Query:  " . $query . "<br>";
                            if (!$fail) {
                                // Pass the query to add the user
                                if (!mysqli_query($mysqli, $query)) {
                                    echo "Add failed: " . $query . "<br>\n";
                                } else {
                                    // Tell the user of the success
                                    echo "<h4>Item Added!</h4><br>\n";
                                    echo "<dl>\n";
                                    echo "<dt>Name</dt> <dd>" . $name . "</dd>\n";
                                    echo "<dt>Type</dt> <dd>" . $type . "</dd>\n";
                                    echo "<dt>Name</dt> <dd>" . $condition . "</dd>\n";
                                    echo "<dt>Type</dt> <dd>" . $type . "</dd>\n";

                                    if (strcmp($type, "book") == 0) {
                                        echo "<dt>Book length</dt> <dd>" . $length . "</dd>\n";
                                        echo "<dt>ISBN</dt> <dd>" . $ISBN . "</dd>\n";
                                    } else {
                                        echo "<dt>Accessories</dt> <dd>" . $accessories . "</dd>\n";
                                        if (strcmp($type, "computer") == 0 || (strcmp($type, "tablet") == 0)) {
                                            echo "<dt>Operating System</dt> <dd>" . $OS . "</dd>\n";
                                            echo "<dt>Manufacturer</dt> <dd>" . $man . "</dd>\n";
                                        }
                                    }
                                    echo "<dt>Description</dt> <dd>" . $description . "</dd>\n";
                                    echo "</dl>\n";
                                }
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
