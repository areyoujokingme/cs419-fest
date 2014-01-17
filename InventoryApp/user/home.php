<!DOCTYPE html>
<?php 
session_start();
if ($_SESSION['logged_in']==0) {
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../index.php">';    
    exit;
} else {
	$username = $_SESSION['username'];
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
    <title>Inventory Locator<title>
    <link href="../../dist/css/bootstrap.css" rel="stylesheet">
    <link href="jumbotron-narrow.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="#">Item Lookup</a></li>
          <li><a href="add.html">Add Item</a></li>
          <li><a href="check.html">Check In/Out</a></li>
        </ul>
        <h3 class="text-muted">Inventory Locator</h3>
      </div>

      <div class="jumbotron">
        <h1>Search bar goes here</h1>
        <p class="lead">This is where the results will appear.</p>
        <p><a class="btn btn-lg btn-success" href="#" role="button">This button could be the search magnifying glass.</a></p>
      </div>

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
</html>
