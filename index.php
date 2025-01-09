<?php
require './scripts/funcdefs.php';
if ($_GET['reset']??false){
	global $db;
	$db=new DB;
  $db->savedb();
}

?>
<html>
<head>
<title>PHPBBg</title>
<link rel="stylesheet" type="text/css" href="style.css" /> 
</head>
<body>
<?php 
//printMenu();
//action post if VVV
?>
<h1>Welcome to <a class="nlk" href="/docs/about.html">phpBBS</a> !</h1>
<div>
<?php //Show latest posts  ?>
</div>
</body>
</html>
