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

$getFlag = false;
if (isset($_GET["{CODE}"])) {
    $unparsedBarcode = filter_input(INPUT_GET, "{CODE}", FILTER_SANITIZE_SPECIAL_CHARS);
}if (isset($_GET["result"])) {
    $unparsedBarcode = filter_input(INPUT_GET, "result", FILTER_SANITIZE_SPECIAL_CHARS);
}

$spacer = "-";
if (isset($_SESSION["spacer"])) {
    $spacer = $_SESSION["spacer"];
} else {
    $_SESSION["spacer"] = $spacer;
}

if (isset($unparsedBarcode)) {
    for ($i = strlen($unparsedBarcode); (($i > 0) && !isset($barcode)); $i--) {
        if ($unparsedBarcode[$i] == $spacer) {
            $barcode = substr($unparsedBarcode, 0, $i);
            $index = substr($unparsedBarcode, $i + 1) + 0;
        }
    }
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
        </style>
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
                <div id="search_results_div" class="jumbotron">
                    <button>
                        <a href="http://zxing.appspot.com/scan?ret=http://web.engr.oregonstate.edu/~vaseka/InventoryApp/admin/scanner.php?result={CODE}">
                            Open Scanner
                        </a>
                    </button>
                    <form class="navbar-form" action="process_in_out.php" method="post" enctype="multipart/form-data">
                        <fieldset class="center_text">
                            <legend>Scan an Item to check in or check out</legend>
                            <ul>
                                <li>
                                    <label for="barcode_input">Barcode</label>
                                    <input id="barcode_input" name="barcode_input" type="text" <?php
                                    if (isset($barcode)) {
                                        echo "value=\"" . $barcode . "\"";
                                    }
                                    ?> >
                                </li>
                                <li>
                                    <label for="barcode_input">Index</label>
                                    <input id="quantity_index_input" name="quantity_index_input" type="text" <?php
                                    if (isset($index)) {
                                        echo "value=\"" . $index . "\"";
                                    }
                                    ?> >
                                </li>
                                <li>
                                    <label for="barcode_input">Username</label>
                                    <input id="username_input" name="username_input" type="text"  >
                                </li>
                                <li>
                                    <button id="search_button" class="btn btn-success" type="submit"><img src="search_bar.jpg"></button>
                                </li>
                        </fieldset>>
                    </form>
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
