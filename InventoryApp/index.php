<?php

session_start();

$flag=false;

if (isset($_SESSION["permissions"])) {
    if (isset($_SESSION['username']) && $_SESSION['permissions'] == 0) {
        $_SESSION['logged_in_inventory_app_cs419'] = 1;
        header('Location: admin/admin.php');
        $flag=true;
    } else if (isset($_SESSION['username']) && $_SESSION['permissions'] == 1) {
        $_SESSION['logged_in_inventory_app_cs419'] = 1;
        header('Location: user/user.php');
        $flag=true;
    }
} 
if(!$flag){
    $_SESSION['logged_in_inventory_app_cs419'] = 0;
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['create_username']);
    unset($_SESSION['create_password']);
    unset($_SESSION['password_verify']);
    header('Location: ./signin/sign_in.php');
}
?>
