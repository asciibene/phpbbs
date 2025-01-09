<?php
	require './scripts/funcdefs.php';
?><html>
<head>
<title>phpwiki - login</title>
<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
	<?php 
  if(empty($_POST)): ?>
	 <h3>Login</h3>
		<form method="post" action="./login.php">
		<input type="text" name="login_name" placeholder="username" />
		<br />
		<input type="password" name="login_password" placeholder="password" />
		<br>
		<button name="action" value="login" type="submit">Login</button>
	</form>
<?php 
  elseif($_POST['action'] == 'login' and verifylogin() ):
     notify('now logged in');			 
  endif;
?>
</body>
</html>
