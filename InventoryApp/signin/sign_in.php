<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['logged_in_inventory_app_cs419'])) {
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../logout.php">';
    exit;
}
if ($_SESSION['logged_in_inventory_app_cs419'] == 1 && (!isset($_SESSION['username']) )) {
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../logout.php">';
    exit;
}
if (isset($_SESSION['username']) && ($_SESSION['logged_in_inventory_app_cs419'] == 1)) {
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../index.php">';
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

    // Check if user entered name and password
    if ((!empty($_POST["username"])) && (!empty($_POST["password"]))) {
        // Store post values
        $username = filter_input(INPUT_POST,'username', FILTER_SANITIZE_EMAIL);
        $_SESSION['username'] = $username;
        if ($_SESSION['username'] == "areyoujokingme@gmail.com" ||
                $_SESSION['username'] == "teachasda@gmail.com" ||
                $_SESSION['username'] == "vasekas@gmail.com" ||
                $_SESSION['username'] == "dmcgrath@eecs.oregonstate.edu") {
            $_SESSION['permissions'] = 0;
        } else {
            $_SESSION['permissions'] = 1;
        }
        $password = filter_input(INPUT_POST,'password', FILTER_SANITIZE_MAGIC_QUOTES);
        $comparename = NULL;
        $comparepassword = NULL;
        //$username = $_SESSION['username'];
        // Prepare select
        if (!($stmt = $mysqli->prepare("SELECT username FROM SiteUsers WHERE username='$username'"))) {
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
            if ($rowcount == 0) {
                //if username doesn't exist, ask them to create account
                $myerrno = 3;
            } else {
                //if it does exist, check to see that entered password matches password in database for given username
                $mysuccessno = 3;
                //echo "Username found.<br>";
                // Prepare select
                if (!($stmt = $mysqli->prepare("SELECT password FROM SiteUsers WHERE username='$username'"))) {
                    //echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                    $myerrno = 1;
                } else {
                    $mysuccessno = 1;
                    //echo "Prepared password select successfully.<br>";
                }
                // Execute select
                if (!$stmt->execute()) {
                    //echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                    $myerrno = 2;
                } else {
                    //execute was successful, now bind result
                    $mysuccessno = 2;
                    //echo "Execute password select successfully.<br>";
                    if (!$stmt->bind_result($comparepassword)) {
                        //echo "Bind failed: " . $stmt->errno . " " . $stmt->error;
                        $myerrno = 4;
                    } else {
                        //bind passed so comparepassword should have something not null in it
                        $mysuccessno = 4;
                        //echo "Bound password select successfully.<br>";
                        $stmt->store_result();
                        $stmt->fetch();
                        //echo "Password found: " . $comparepassword . "<br>";
                        //echo "Posted password was: " . $password . "<br>";
                        $stmt->close();
                        $compare_hashed_password = crypt($password, CRYPT_SHA256);
                        if ($compare_hashed_password != $comparepassword) {
                            $myerrno = 5;
                            //echo "It says they are not the same.<br>";
                        } else {
                            //they may enter the main site now
                            $mysuccessno = 5;
                            //echo "ALL SYSTEMS GO!<br>";
                            unset($_SESSION['username']);
                            $_SESSION['username'] = $username;
                            $_SESSION['logged_in_inventory_app_cs419'] = 1;
                        }
                    }
                }
            }
        }
        //echo "Mysuccessno: " . $mysuccessno. "<br>";
    }
    //echo '<pre>' . htmlspecialchars(print_r(get_defined_vars(), true));
    //var_dump($_SESSION);
    if (isset($_SESSION['username']) && ($_SESSION['logged_in_inventory_app_cs419'] == 1)) {
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../index.php">';
        exit;
    }
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
        <title>Sign In</title>
        <link href="../dist/css/bootstrap.css" rel="stylesheet">
        <link href="signin.css" rel="stylesheet">
        <script src="jquery-1.10.2.js"></script>
        <script src="jquery.validate.min.js"></script>
        <script src="../dist/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            //shows and hides forms and buttons
            $(document).ready(function() {
                $("#message").hide();
                $("#message_success").hide();
                $("#message_success").text("Is this working?");
                $("#change_password").validate({
                    rules: {
                        password: {
                            required: true,
                            minlength: 6,
                            maxlength: 8
                        },
                        username: {
                            required: true,
                            email: true
                        }
                    }
                });
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
                        $("#message_success").text("All checks passed. You may enter the site now.");
                        $("#message_success").show();
                        $("#message").hide();
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
                        $("#message").html("Username does not exist. Please <a href='create_new_account.php'>create an account.</a>");
                        $("#message").show();
                        break;
                    case 4:
                        $("#message").text("Bind failed.");
                        $("#message").show();
                        break;
                    case 5:
                        $("#message").html("Incorrect password. Please <a href='login.php'>try again</a> or <a href='create_account.php'>create an account.</a>");
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
                    <li class="active"><a href="#">Sign In</a></li>
                    <li><a href="create_new_account.php">Create Account</a></li>
                </ul>
                <h3 class="text-muted">Inventory Locator</h3>
            </div>
            <form id="sign_in" class="form-signin" action = "sign_in.php" method="POST" enctype="multipart/form-data">
                <h2 class="form-signin-heading">Sign In</h2>
                <input name="username" type="email" class="form-control" placeholder="Email address" required autofocus>
                <input name="password" type="password" class="form-control" placeholder="Password" required>
                <label class="checkbox">
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button><br>
                <a href="change_password.php">Forgot Password?</a>
            </form>
            <div id="message" style="margin-left:50px"></div><br>
            <div id = "message_success" style="margin-left:50px"></div>
        </div>
    </body>
</html>
