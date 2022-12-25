<?php
require 'funcdefs.php';
initdb();
?><html>
<head>
	<title>phpwiki</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
	<?php printLogo(); ?>
	<?php 
	 if(isset($_COOKIE["verified"])){
    echo '<small>Logged in as '.$_COOKIE["verified"].'. </small><br>';
  }
  if(isset($_POST['user_comment']) and isset($_COOKIE['verified'])){
    addcomment($_GET['index'],$_POST['user_comment']);
  }elseif(!isset($_COOKIE['verified']) and isset($_POST['user_comment'])){
    printError("You are not logged in.");
  }
?>
<?php if (isset($_GET['index'])): ?>
   <?php viewpage($_GET['index']); ?>
  <h3>add a comment</h3>
     <?php echo '<form method="post" action="./view.php?index='.$_GET['index'].'">'; ?> 
      <input type="text" name="user_comment" placeholder="add a comment" />
      <br>
      <button type="submit">Submit</button>
    </form>
<?php endif; ?>
</body>
</html>
