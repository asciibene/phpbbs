<?php
require 'funcdefs.php';
?><html>
<head>
	<title>phpwiki</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
<?php
// make it so when GETuindex is empty, display logged user profile.
    if (isset($_GET['uindex'])){
      viewprofile($_GET['uindex']);
    }elseif(empty($_GET)){   
      printError('No user index supplied');
    }
?>
</body>
</html>
