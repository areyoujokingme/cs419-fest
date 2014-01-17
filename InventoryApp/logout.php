<?php 
session_start();
unset($_SESSION['username']);
$_SESSION['logged_in']=0;
session_destroy();
header('Location: index.php');
?>

