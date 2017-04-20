<? 
/* Include Files *********************/
include("functions.php"); 
/*************************************/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Registration Page</title>
<link href="style.css" rel="stylesheet" type="text/css" media="screen"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body>

<div id="main">


<div id="tab">


<center><a href="index.php">Log in</a></center>
</div>
<div id="tab2">
<center><a href="register.php">Join/Register</a></center>
</div>
<div id="container">
<center>
<div class="loginregister">
<h2>Reset Password</h2>
<form action="passreset.php" method="post">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Username : </td><td><input type="text" name="username" maxlength="30"></td></tr>
<tr><td>A.E.M. : </td><td><input type="text" name="aem" maxlength="4"></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="respass" value="Reset"></td></tr>
</table>
</form>
</div>
<div>
<br />An e-mail will be sent to your university mail based on your A.E.M.
<br />(example "st0111@icte.uowm.gr"). In that e-mail you will receive 
<br />a new randomly generated password which you can 
<br />change latter to something you remember. <br />
<br /><a href=http://webmail.uowm.gr/students/ > http://webmail.uowm.gr/students/ </a>
</div>
<br />
</center>
<br /> <br />
<center>
<div class="loginregister">
<h2>Remind Username</h2>
<form action="passreset.php" method="post">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>A.E.M. : </td><td><input type="text" name="aemremind" maxlength="4"></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="usernameremind" value="Send"></td></tr>
</table>
</form>
</div>
<div>
<br />An e-mail with your username will be sent in your university mail 
<br />based on your A.E.M. (example "st0111@icte.uowm.gr").<br />
<br /><a href=http://webmail.uowm.gr/students/ > http://webmail.uowm.gr/students/ </a>
</div>
<br />
</center>
</div>
</div>
<br />
<?php require("footer.html"); ?>
</body>
</html>
