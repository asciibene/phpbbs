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
	<?php if(isset($_COOKIE["verified"])){
          echo '<small>Logged in as '.$_COOKIE["verified"].'. </small><br>';
        } ?>

<?php
    if (isset($_GET['uindex'])){
      viewprofile($_GET['uindex']);
    }elseif(empty($_GET)){   
      // show all user ?
    }
?>
</body>
</html>
