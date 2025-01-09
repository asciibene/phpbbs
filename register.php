<?php
require './scripts/funcdefs.php';
global $db;
?><html>
<head>
<title>phpbbs - registration</title>
<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
  <?php if(empty($_POST)): ?>
	   <h3>Registration ₣orm</h3>
    <form method="post" action="./register.php">
      <input type="text" name="reg_name" placeholder="username" />
      <br>
      <input type="password" name="reg_password" placeholder="pwd" />
      <br>
      <input type="radio" id="captcha_yes" name="reg_captcha" value="yes" >
			<label for="captcha_yes">Yes</label>
			</input>
      <input type="radio" id="captcha_no" name="reg_captcha" value="no">
				<label for="captcha_no">no</label>
      </input>
      <button type="submit" name="action" value="reg">Send</button>
    </form>
  <?php elseif($_POST['action'] == "reg" and $db->new_user() == true): ?>
		<?php notify('Registration success'); ?>
	<?php endif;
?>

</body>
</html>
