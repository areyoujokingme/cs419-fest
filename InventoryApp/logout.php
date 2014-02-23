<?php 
session_start();
unset($_SESSION['username']);
$_SESSION['logged_in_inventory_app_cs419']=0;
session_destroy();
header('Location: index.php');
?>

