<div id="main">

<div id="tab">

<center>Log in</center>

</div>

<div id="tab2">

<center><a href="register.php">Join/Register</a></center>

</div>

<div id="container">

<? postpublicann(); ?>

<center>

<div class="loginregister">

<h2>Log in</h2>

<form action="" method="post">

<table align="left" border="0" cellspacing="0" cellpadding="3">

<tr><td>Username:</td><td><input type="text" name="user" maxlength="30"></td></tr>

<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30"></td></tr>

<tr><td colspan="2" align="left"><input type="checkbox" name="remember">

<font size="2">Remember me next time</td></tr>

<tr><td colspan="2" align="right">
<?
if(isset($_GET['entry']) && $_GET['entry']=='igrades'){
	echo "<input type='hidden' name='redirect' value='igrades'>";
}
if(isset($_GET['entry']) && $_GET['entry']=='iquiz'){
	echo "<input type='hidden' name='redirect' value='iquiz'>";
}
if(isset($_GET['entry']) && $_GET['entry']=='itasks'){
	echo "<input type='hidden' name='redirect' value='itasks'>";
}
if(isset($_GET['entry']) && $_GET['entry']=='iexams'){
	echo "<input type='hidden' name='redirect' value='iexams'>";
}
if(isset($_GET['entry']) && $_GET['entry']=='iexamsII'){
	echo "<input type='hidden' name='redirect' value='iexamsII'>";
}

if(isset($_GET['entry']) && $_GET['entry']=='ipresence'){
	echo "<input type='hidden' name='redirect' value='ipresence'>";
}

if(isset($_GET['entry']) && $_GET['entry']=='administration'){
	echo "<input type='hidden' name='redirect' value='administration/?side_menu=view_users'>";
}
?>
<input type="submit" name="sublogin" value="Login">
</td></tr>

</table>

</form>

</div>

<br /><div id='currentdateout'> <br />Δεν υπάρχει ενεργή εξέταση <br /><br />αυτή τη στιγμή.</div>

<br />

<span><font size="+1"><a href='register.php'> Register/Join/Εγγραφή</a></font></span><br /><br />

<span><font size="+1"><a href='passreset.php'> Forgot Password/Request Password Reset/Remind Username</a></font></span><br /><br />

<span><a href='printschedule.php?all'> Προβολή όλων των χρονοθυρίδων </a><span><br /><br />

<!--
<span><img src="images/phone-cell-icon.png" style="margin-bottom: -7px;"><a href="mobile/index.php" style="color: #fa4;">Mobile mode</a><span>
-->
<span><button onclick="myFunction()">Δήλωση προσωπικών δεδομένων</button> <script>
function myFunction() {
    window.open("dpa.html");
}
</script>
</span>
<br />

</center>

<br />

</div>
<!--
<div id="twitter"> <?php require("twitter.php.inc");?> </div>
-->

</div>
