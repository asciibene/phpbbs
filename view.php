<?php
require 'funcdefs.php';
initdb();
?><html>
<head>
	<title>phpwiki</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
	<h1><span id="logo1">php</span><span id="logo2">wiki</span> </h1>
	<?php 
	 if(isset($_COOKIE["verified"])){
    echo '<small>Logged in as '.$_COOKIE["verified"].'. </small><br>';
  }
  if(isset($_POST['user_comment'])){
    addcomment($_GET['index'],$_POST['user_comment']);
  }
               
?>
<?php if (isset($_GET['index'])){
        viewpage($_GET['index']);} ?>
<?php if(isset($_COOKIE['verified']) and isset($_GET['index'])): ?>
  <h3>add a comment</h3>
     <?php echo '<form method="post" action="./view.php?index='.$_GET['index'].'">'; ?> 
      <input type="text" name="user_comment" placeholder="add a comment" />
      <br>
      <button type="submit">Submit</button>
    </form>
<?php elseif(empty($_GET) and empty($_POST)): ?>
      <?php showtitles(); ?>
<?php endif; ?>
</body>
</html>
