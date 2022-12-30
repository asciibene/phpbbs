<?php
require 'funcdefs.php';

?><html>
<head>
	<title>phpbbs - admin</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
<h1>Admin</h1>
<?php if(!empty($_POST['user_title']) and !empty($_POST['user_content']) and isset($_COOKIE["verified"])): ?>

<?php elseif(!empty($_POST['user_title']) and !empty($_POST['user_content']) and empty($_COOKIE["verified"])): ?>
   <?php printError('You are not connected'); ?>
<?php endif; ?>
<div>

</div>
</body>
</html>
