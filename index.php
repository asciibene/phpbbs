<?php
require 'funcdefs.php';
// DELETE THIS WHEN DEPLOYING
if (isset($_GET['reset'])){
  global $db;
  global $dbusr;
  $db=[];
  $dbusr=[];
  savedb();
  initdb();
}else {
  initdb();
}
//XXXXXXXXXXXXXXXXXXX

?><html>
<head>
	<title>phpwiki</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
<?php if(isset($_COOKIE["verified"])){
        printMenu();
}
?>
<?php if(!empty($_POST['user_title']) and !empty($_POST['user_content']) and checksec($_COOKIE["uindex"],$_COOKIE["hash"]) and $_GET['a']=='newpost'): ?>
  <?php newpage($_POST['user_title'],$_POST['user_content']); ?>
<?php elseif(!empty($_POST['user_title']) and !empty($_POST['user_content']) and checksec($_COOKIE["uindex"],$_COOKIE["hash"])==false): ?>
   <?php printError('You are not connected'); ?>
<?php endif; ?>
<h1>Welcome to <a class="nlk" href="/docs/about.html">phpBBS</a> !</h1>
<div>

<?php homepage(); ?>
</div>
</body>
</html>
