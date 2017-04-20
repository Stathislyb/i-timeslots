<?
session_start(); 
include("functions.php"); 

/**
 * Displays the appropriate message to the user
 * after the registration attempt. It displays a 
 * success or failure status depending on a
 * session variable set during registration.
 */
function displayStatus(){
   $uname = $_SESSION['reguname'];
   if($_SESSION['regresult']){
?>

<h1>Registered!</h1>
<p>Thank you <b><? echo $uname; ?></b>, your information has been added to the database, you may now <a href="index.php" title="Login">log in</a>.</p>

<?
registrationMail($_SESSION['aem']);
   }
   else{
?>

<h1>Registration Failed</h1>
<p>We're sorry, but an error has occurred and your registration for the username <b><? echo $uname; ?></b>, could not be completed.<br>
Please try again at a later time.</p>

<?
   }
   unset($_SESSION['reguname']);
   unset($_SESSION['registered']);
   unset($_SESSION['regresult']);
   unset($_SESSION['aem']);
   unset($_SESSION['telephone']);
}

if(isset($_SESSION['registered'])){
/**
 * This is the page that will be displayed after the
 * registration has been attempted.
 */
?>

<html>
<head>
<title>Registration Page</title>
<link href="style.css" rel="stylesheet" type="text/css" media="screen"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body>
<div id="main">
<div id="container">
<center>
<? displayStatus(); ?>
</center>
</div></div>
<br />
<?php require("footer.html"); ?>
</body>
</html>

<?
   return;
}

/**
 * Determines whether or not to show to sign-up form
 * based on whether the form has been submitted, if it
 * has, check the database for consistency and create
 * the new account.
 */
if(isset($_POST['subjoin'])){



   /* Make sure all fields were entered */
   if(!$_POST['user'] || !$_POST['pass']){
      die('You didn\'t fill in a required field.');
	  exit();
   }



 	//Αυτό εκτελείται όταν έχουμε λάβει POST  
   if(!verify_uowm_ip()){
	   die('You can not post this form outside the bounds of the University of Western Macedonia. Your IP seems to be: '.$_SERVER["REMOTE_ADDR"].' TIP: You can use the VPN service of UOWM ( <a href="http://noc.uowm.gr/www/services/vpn/"> http://noc.uowm.gr/www/services/vpn/ </a> ) ');
	   exit();
   }




   /* Spruce up username, check length */
   $_POST['user'] = mysql_escape_string(filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING));
   if(strlen($_POST['user']) > 25){
      die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
<center>Sorry, the username is longer than 25 characters, please shorten it.
</center></div></div></body>");
   }

   /* Check if username is already in use */
   if(usernameTaken($_POST['user'])){
      $use = $_POST['user'];
      die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
<center>Sorry, the username: <strong>$use</strong> is already taken, please pick another one.
</center></div></div></body>");
   }
   /* Check if AEM is already in use */
   $_POST['aem']=mysql_escape_string(filter_var($_POST['aem'], FILTER_SANITIZE_NUMBER_INT));
	if(aemTaken($_POST['aem'])){
      $aem = $_POST['aem'];
      die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
<center>Sorry, the A.E.M.: <strong>$aem</strong> is already in use, if someone else is using yours, contact the administration.
</center></div></div></body>");
   }
   /* Check if E-mail is valid */
   $_POST['email']=mysql_escape_string(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
   $_POST['department'] = mysql_escape_string(filter_var(trim($_POST['department']), FILTER_SANITIZE_NUMBER_INT));
   if($_POST['department']==1){
	   if(substr($_POST['email'], -13) != '@icte.uowm.gr'){
		   die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'><center>Sorry, the E-mail: <strong>".$_POST['email']."</strong> is not valid for the department you selected.</center></div></div></body>");
	   }
   }
   if($_POST['department']==2){
	   if(substr($_POST['email'], -13) != '@mech.uowm.gr'){
		   die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'><center>Sorry, the E-mail: <strong>".$_POST['email']."</strong> is not valid for the department you selected.</center></div></div></body>");
	   }
   }
   if($_POST['department']==3){
	   if(substr($_POST['email'], -12) != '@csd.auth.gr'){
		   die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'><center>Sorry, the E-mail: <strong>".$_POST['email']."</strong> is not valid for the department you selected.</center></div></div></body>");
	   }
   }
   /* Add the new account to the database */
   $_SESSION['aem']= $_POST['aem'];
   $_SESSION['email']= $_POST['email'];
   $_SESSION['ran'] = mt_rand(0,9999);
   $_POST['pass'] = mysql_escape_string(filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING));
   $md5pass = md5($_POST['pass']);
   $_SESSION['reguname'] = $_POST['user'];
   $_POST['first_name'] = mysql_escape_string(filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING));
   $_POST['last_name'] = mysql_escape_string(filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING));
   $_SESSION['telephone'] = mysql_escape_string(filter_var(trim($_POST['telephone']), FILTER_SANITIZE_NUMBER_INT));
   $_SESSION['regresult'] = addNewUser($_POST['user'], $md5pass, $_POST['first_name'],$_POST['last_name'],$_POST['aem'],$_SESSION['ran'],$_SESSION['telephone'],$_POST['department'],$_POST['email']);
   $_SESSION['registered'] = true;
   echo "<meta http-equiv=\"Refresh\" content=\"0;url=$_SERVER[PHP_SELF]\">";
   return;
}
else{
/**
 * This is the page with the sign-up form, the names
 * of the input fields are important and should not
 * be changed.
 */
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
<center>Join/Register</center>
</div>

<div id="container">
<center>

<div class="loginregister">
<h2>Register</h2>
<?php if(verify_uowm_ip()){ ?>
	<form action="register.php" method="post">
	<table align="left" border="0" cellspacing="0" cellpadding="3">
	<tr><td>Username <br/ >(latin characters): </td><td><input type="text" name="user" maxlength="25"></td></tr>
	<tr><td>Password <br />(latin/greek/numbers): </td><td><input type="password" name="pass" maxlength="25"></td></tr>
	<tr><td>First Name <br />(greek characters): </td><td><input type="text" name="first_name" maxlength="25"></td></tr>
	<tr><td>Last Name <br />(greek characters): </td><td><input type="text" name="last_name" maxlength="25"></td></tr>
	<tr><td>A.E.M. <br/ >(numbers): </td><td><input type="text" name="aem" maxlength="4"></td></tr>
	<tr><td>E-mail <br/ >(accademic mail): </td><td><input type="text" name="email" maxlength="25"></td></tr>
	<tr>
	<td>Department :</td><td><select name='department'>
		<?php
		global $conn;
		$query_dep = "SELECT departments.*,universities.shortname AS univ_name FROM departments JOIN universities ON departments.universityID = universities.id";
		$rslt_dep = mysql_query($query_dep,$conn);
		while ($departments= mysql_fetch_array($rslt_dep)){
			echo "<option value='".$departments['id']."'>".$departments['shortname']." (".$departments['univ_name'].")</option>";
		}
		?>
	</select></td>
	<tr><td>Phone number <br/ >(optional but recommended) : </td><td><input type="text" name="telephone" maxlength="10" ></td></tr>
	<tr><td colspan="2" align="right"><input type="submit" name="subjoin" value="Join/Register!"></td></tr>
	</table>
	</form>
<?php }else{ ?>	
	Η εγγραφή επιτρέπεται μόνο από IP του Πανεπιστημίου Δυτικής Μακεδονίας (83.212.??) . <br/><br/>
	Αν έχετε δικαίωμα εγγραφής, μπορείτε να χρησιμοποιήσετε την δωρεάν υπηρεσία <a href="http://noc.uowm.gr/www/services/vpn/"> UOWM VPN ( http://noc.uowm.gr/www/services/vpn/ )</a> για να αποκτήσετε IP εντός του ΠΔΜ.<br/>
	<br/>
	<font size=+2>
	Η IP σας είναι <?php echo $_SERVER["REMOTE_ADDR"]; ?> 
	</font>
	<br/>
	<br/>
	<br/>
	Αν η διεύθυνση σας είναι IPv6 δοκιμάστε να συνδεθείτε στην IPv4 διεύθυνση του διακομιστή arch στη διεύθυνση
	<a href="https://archipv4.icte.uowm.gr/schedule/register.php"> https://archipv4.icte.uowm.gr/schedule/register.php </a> 
	(και να δεχθείτε την προειδοποίηση SSL NET::ERR_CERT_COMMON_NAME_INVALID που θα εμφανιστεί και είναι δικαιολογημενη, μέσω
του Advanced->Proceed to archipv4.icte.uowm.gr (unsafe)).
	<br/>

<?php } ?>	
</div>

<div>
<?php if(verify_uowm_ip()){ ?>
	<br /><b>Προσοχή: Μόλις πατήσετε Join μην κλείσετε το παράθυρο, έως ότου να ολοκληρωθεί η αποστολή του email</b>
	<br /><br /><b>Important :</b><h5>A confirmation e-mail will be sent in your univercity mail based on your A.E.M.
	<br />(example "st0111@icte.uowm.gr). It is necessary to confirm your 
	<br />e-mail in order to register any times. </h5>
	<br /><a href=http://webmail.uowm.gr/students/ > http://webmail.uowm.gr/students/ </a>
<?php } ?>	
</div>

<br />
<center>
</div>
</div>
<br />
<?php require("footer.html"); ?>
</body>
</html>


<?
}
?>
