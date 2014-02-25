<!DOCTYPE html>
<?php
session_start();
ini_set('display_errors', 1);
if (false) {
    if ($_SESSION['logged_in_inventory_app_cs419'] == 0) {
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../index.php">';
        exit;
    } else {
        $username = $_SESSION['username'];
    }
} else {
    $username = "Arthur";
    $_SESSION['logged_in_inventory_app_cs419'] = 1;
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
                display:block;
            }
            .center_text {
                text-align:left;
                line-height:normal;
                font-size:initial;
            }
            .center_text li{
                list-style-type: none;
                padding-bottom:10px;
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
                <form id="add_form" class="navbar-form" action="add.php" method="POST" enctype="multipart/form-data">
                    <fieldset class="center_text">
                        <legend>Item to add to the Library</legend>
                        <ul>
                            <li>
                                <label for='add_form'>Item Type</label >
                                <select id="type" form="add_form" name="add_type" onchange="showOptions()">
                                    <option></option>
                                    <option value="computer">Computer</option>
                                    <option value="embedded">Embedded System / Microcontroller</option>
                                    <option value="book">Book</option>
                                    <option value="magazine">Magazine</option>
                                    <option value="tablet">Tablet</option>
                                    <option value="accessory">Electronic Accessory</option>
                                </select>
                            </li>
                            <li>
                                <label for="add_name">Item Name</label>
                                <input id="add_name" name="add_name" type="text" placeholder="Name">
                            </li>
                            <li>
                                <label for="add_quantity"> Quantity</label>
                                <input id="add_quantity" name="add_quantity" type="text">
                            </li>
                            <li class="optional" id="page_length">
                                <label for="add_length">Page Length</label>
                                <input id="add_length" name="add_length" type="text">
                            </li>
                            <li class="optional" id="ISBN">
                                <label for="add_ISBN">ISBN/ISSN</label>
                                <input id="add_ISBN" name="add_ISBN" type="text" placeholder="ISBN or ISSN">
                            </li>
                            <li class="optional" id="OS">
                                <label for="add_OS">Operating System</label>
                                <input id="add_OS" name="add_OS" type="text">
                            </li>
                            <li class="optional" id="manufacturer">
                                <label for="add_man"> Manufacturer</label>
                                <input id = "add_man" name="add_man" type="text">
                            </li>
                            <li class="optional" id="accessories">
                                <label for="add_accessories">Additional Accessories</label>
                                <textarea id="add_accessories" name="add_accessories" rows="2" form="add_form"></textarea>
                            </li>
                            <li>
                                <label for="add_condition">Condition</label>
                                <select id="add_condition" form="add_form" name="add_condition">
                                    <option></option>
                                    <option value="new">New</option>
                                    <option value="very_good">Very Good</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </li>
                            <li>
                                <label for="add_description">Brief Description</label>
                                <textarea id ="add_description" name="add_description" rows="5" form="add_form"></textarea>
                            </li>
                        </ul>
                        <div>
                            <input type="submit" text="Submit">
                        </div>
                    </fieldset>
                </form>
                <div  id="add_confirmation">
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

                            // Get values from user
                            $type = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_type", FILTER_SANITIZE_STRING));
                            $name = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_name", FILTER_SANITIZE_STRING));
                            $barcode = $type . $name . rand();
                            $description = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_description", FILTER_SANITIZE_STRING));
                            $condition = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_condition", FILTER_SANITIZE_STRING));
                            $quantity = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_quantity", FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))));


                            // Generic start to the query
                            $fail = False;
                            $queryDeclare = 'INSERT INTO Item (barcodeID, name, type_of_item, itemQuantity, num_available, description, item_condition';
                            $queryInsert = " VALUES ('" . $barcode . "','" . $name . "','" . $type . "','" . $quantity . "', '" . $quantity . "', '"
                                    . $description . "','" . $condition . "'";

                            // use these in cases of a book / computer
                            if ((strcmp($type, "book") == 0) || (strcmp($type, "magazine") == 0)) {
                                $length = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_length", FILTER_VALIDATE_INT, array("options" => array("min_range" => 0))));
                                $ISBN = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_ISBN", FILTER_SANITIZE_STRING));
                                $queryDeclare = $queryDeclare . ", pages_if_book, ISBN_or_ISSN, quantity_index)";
                                $queryInsert = $queryInsert . ", '" . $length . "', '" . $ISBN . "'";
                            } else if ((strcmp($type, "computer") == 0) || (strcmp($type, "tablet") == 0)) {
                                $accessories = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_accessories", FILTER_SANITIZE_STRING));
                                $OS = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_OS", FILTER_SANITIZE_STRING));
                                $man = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_man", FILTER_SANITIZE_STRING));
                                $queryDeclare = $queryDeclare . ", OS_if_computer, hardware_man, accessories, quantity_index)";
                                $queryInsert = $queryInsert . ", '" . $OS . "', '" . $man . "', '" . $accessories . "'";
                            } else if (strcmp($type, "embedded") == 0) {
                                $man = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_man", FILTER_SANITIZE_STRING));
                                $accessories = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_accessories", FILTER_SANITIZE_STRING));
                                $queryDeclare = $queryDeclare . ", hardware_man, accessories, quantity_index)";
                                $queryInsert = $queryInsert . ", '" . $man . "', '" . $accessories . "'";
                            } else if(strcmp($type, "accessory") == 0 ) {
                                $man = mysqli_real_escape_string($mysqli, filter_input(INPUT_POST, "add_man", FILTER_SANITIZE_STRING));
                                $queryDeclare = $queryDeclare . ", hardware_man, quantity_index)";
                                $queryInsert = $queryInsert . ", '" . $man . "'";
                            } else {
                                $fail = True;
                                echo "Bad type, please try again<br>\n";
                            }

                            $spacer = "-";
                            if (isset($_SESSION["spacer"])) {
                                $spacer = $_SESSION["spacer"];
                            } else {
                                $_SESSION["spacer"] = $spacer;
                            }

                            $barcodeSite = "http://chart.googleapis.com/chart?chs=300x300&cht=qr&choe=UTF-8&chld=H&chl=";
                            $barcodeSite = $barcodeSite . $barcode;

                            //echo "Query:  " . $query . "<br>";
                            if (!$fail) {
                                // Pass the query to add the user
                                for ($i = 0; $i < $quantity; $i++) {
                                    $query = $queryDeclare . $queryInsert . ", '" . $i . "');";
                                    //echo "<p>\n" . $query . "</p>\n";
                                    if (!mysqli_query($mysqli, $query)) {
                                        echo "Add failed: " . $query . "<br>\n";
                                        $fail = true;
                                    }
                                }
                                if (!$fail) {
                                    // Tell the user of the success
                                    echo "<h4>Item Added!</h4>\n";
                                    echo "<div class=\"center_text\">\n";
                                    echo "<ul>\n";
                                    echo "<li><label for=\"type_added\">Type</label> <span id=\"type_added\">" . $type . "</span></li>\n";
                                    echo "<li><label for=\"name_added\">Name</label> <span=\"name_added\">" . $name . "</span></li>\n";
                                    echo "<li><label for=\"num_added\">Quantity</label> <span=\"num_added\">" . $quantity . "</span></li>\n";
                                    if (isset($length)) {
                                        echo "<li><label for=\"length_added\">Book length</label> <span id=\"length_added\">" . $length . "</span></li>\n";
                                    }
                                    if (isset($ISBN)) {
                                        echo "<li><label for=\"isbn_added\">ISBN</label> <span id=\"isbn_added\">" . $ISBN . "</span></li>\n";
                                    }
                                    if (isset($accessories)) {
                                        echo "<li><label for=\"access_added\">Accessories</label> <span id=\"access_added\">" . $accessories . "</span></li>\n";
                                    }
                                    if (isset($OS)) {
                                        echo "<li><label for=\"os_added\">Operating System</label> <span id=\"os_added\">" . $OS . "</span></li>\n";
                                    }
                                    if (isset($man)) {
                                        echo "<li><label for=\"man_added\">Manufacturer</label> <span id=\"man_added\">" . $man . "</span></li>\n";
                                    }

                                    echo "<li><label for=\"con_added\">Condition</label> <span id=\"con_added\">" . $condition . "</span></li>\n";
                                    echo "<li><label for=\"des_added\">Description</label> <span id=\"des_added\">" . $description . "</span></li>\n";
                                    echo "<li><label for=\"barcodes\">Barcodes</label>\n";
                                    echo "<ol id=\"barcodes\">\n";
                                    for ($i = 0; $i < $quantity; $i+=1) {
                                        echo "<li><a href=\"" . $barcodeSite . $spacer . $i . "\" target=\"_blank\">Barcode " . $i . "</a> </li>\n";
                                    }
                                    echo "</ol>\n";
                                    echo "</li>\n";
                                    echo "</ul>\n";
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
                case "embedded":
                    $("#accessories").removeClass("optional");
                    $("#accessories_box").removeClass("optional");
                case "accessory":
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