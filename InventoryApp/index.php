<?php 
session_start();

if (isset($_SESSION['username']) && $_SESSION['permissions']==0) {
	$_SESSION['logged_in'] = 1;
	header('Location: http://web.engr.oregonstate.edu/~ashmorel/419/InventoryApp/admin/admin.php');
} else if (isset($_SESSION['username']) && $_SESSION['permissions']==1) {
	$_SESSION['logged_in'] = 1;
	header('Location: http://web.engr.oregonstate.edu/~ashmorel/419/InventoryApp/user/user.php');
} else {
	$_SESSION['logged_in'] = 0;
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['create_username']);
	unset($_SESSION['create_password']);
	unset($_SESSION['password_verify']);
	header('Location: sign_in.php');
}
?>