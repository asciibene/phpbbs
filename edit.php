<html>
<head>
	<title>phpwiki - new page</title>
	<link rel="stylesheet" type="text/css" href="style.css" />  
</head>
<body>
	<h1><span id="logo1">php</span><span id="logo2">wiki</span> </h1>
	<h3>New page</h3>
    <form method="post" action="./index.php">
      <input type="text" name="user_title" placeholder="Title" />
    <br>
      <textarea id="cnt" name="user_content">default text</textarea><br>
  <button type="submit">Send</button>
</form>
</body>
</html>