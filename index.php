<?php
require 'funcdefs.php';
// DELETE THIS WHEN DEPLOYING
if (isset($_GET['reset'])){
  $db=[];
  $usrdb=[['n'=>'test','p'=>'wow']];
  savedb();
}else{
  initdb();
}
//XXXXXXXXXXXXXXXXXXX

?><html>
<head>
	<title>phpwiki</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
<?php printLogo(); ?>
	<?php if(isset($_COOKIE["verified"])){
          echo '<small>Logged in as '.$_COOKIE["verified"].'. </small><br>';
        } ?>
<?php if(!empty($_POST['user_title']) and !empty($_POST['user_content']) and isset($_COOKIE["verified"])): ?>
  <?php newpage($_POST['user_title'],$_POST['user_content']);
        echo '<p> Created new page '.$_POST['user_title'].' </p>'; ?>
<?php elseif(!empty($_POST['user_title']) and !empty($_POST['user_content']) and empty($_COOKIE["verified"])): ?>
   <?php printError('You are not connected'); ?>
<?php endif; ?>
<div>
<?php showtitles(); ?>
</div>
</body>
</html>
