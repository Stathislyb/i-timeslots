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
<h2>Resend the activation mail</h2>
<form action="passreset.php" method="post">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>A.E.M. : </td><td><input type="text" name="user_aem" maxlength="4"></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="reactivmail" value="Submit"></td></tr>
</table>
</form>
</div>
<div>
<br />An e-mail will be sent in your univercity mail based on your A.E.M.
<br />(example "st0111@icte.uowm.gr) with your activation link.
<br />
<br />You have to activate your account in order to log in.<br />
<br /><a href=http://webmail.uowm.gr/students/ > http://webmail.uowm.gr/students/ </a>
</div>
<br />
</center>
<br /> <br />
</div>
</div>
<br />
<?php require("footer.html"); ?>
</body>
</html>
