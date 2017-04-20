<?php
//var_dump($_POST);die();
?>
<?php
include("database.php");
if(!isset($_SESSION)) {
     session_start();
}

/** 
 * Login Functions. Sanitize happens as soons as they sent the $_POST[] so it's not needed again here.
 * Only username and password are sent from the user so they are the only 2 sanitized 
 * (aside the case we retrive everything from cookies to seession).
 */


 
/**
 * Determines whether or not to display the login
 * form or to show the user that he is logged in
 * based on if the session variables are set.
 */
 
function checkLLogin()
{
   // LogOut after 2 hours (7200 sec) 
   if (isset($_SESSION['Login_expire']) && (time() - $_SESSION['Login_expire'] > 7200)) {
       session_unset();      
       session_destroy();   
   	   header( 'Location: index.php' ) ;
	   echo '<meta http-equiv="refresh" content="0; url=index.php" />';
   }
}


function displayLogin(){
	global $conn;
	$q = "SELECT * FROM days";
	$rslt = mysql_query($q,$conn);	
	global $totaltabs;
	while ($rows= mysql_fetch_array($rslt)){
			$totaltabs++;
	}
   // LogOut after 2 hours (7200 sec) 
   if (isset($_SESSION['Login_expire']) && (time() - $_SESSION['Login_expire'] > 7200)) {
       session_unset();      
       session_destroy();   
   	   header( 'Location: index.php' ) ;
	   echo '<meta http-equiv="refresh" content="0; url=index.php" />';
   }

   
   global $logged_in;
   if($logged_in){   /* If loged in . */
   echo "<meta http-equiv='refresh' content='900'>"; // refresh every 15 minutes ( 900 sec)
   
   pruning();
   pruningslots();
   autoenable();
   autorecurring();
    echo "<div id='header_text'><div class='welcomemsg'>Welcome <b>$_SESSION[username]</b>, you are logged in.";
	echo "<script type='text/javascript'>new imageclock.display();</script>";
	echo "</div> <div class='logoutheader'><a href='logout.php'>Logout</a> </div></div>";
	  
	  if($_SESSION['type']==1){
		echo"<form class='emptyallicon' action='index.php' method='post'><input title='Άδειασμα Όλων των χρονοθυρίδων όλων των καρτελών' type='image' src='images/recycle-binx60.png' width='60' height='60' name='Delete' alt='Clean All' value='Clean Dates'></form> ";
		echo"<form class='icalcallicon' action='index.php' method='post'><input type='hidden' name='ics_admin' value='1'><input title='Αποστολή Όλων των ειδοποιήσεων ICS' type='image' src='images/icalc-60.png' width='60' height='60' name='Icalc' alt='Send Icalc for all' value='Icalc All'></form> ";
	  }else {
	  echo"<div style='width:60px; height:60px'></div> ";
	  }
	 echo "<div id='currentdate'> </div>";
	 show_comments_user();
	 echo "<div id='global_ann_container'>";
	 postpublicann(); 
	 echo "</div>";
	 echo "<div id='main'>";
	 showdaystabs();
	 //echo "<div id='tab2'><span id='innermenu'><a href='javascript:#' onclick='showusercp($totaltabs)'>User CP</a></span></div><div id='container'><br/>";
	 echo "<div id='tab2'><span id='innermenu'><a href='javascript:showusercp($totaltabs)'>User CP</a></span></div><div id='container'><br/>";



	showschedulesperday();
	
	echo " <div id='usercpmain'> ";
	
    echo "<div class='cps'>";
	include("usercp.php");   			 /* Call the forms for User CP . */

	include("admincp.php");   			 /* Call the forms for Admin CP . */
	echo "</div>";
	
	echo " </div></div></div>";	
	
   }
   else{		/* If not loged in . */
	include("login.php");   			 /* Call the Log in form . */
   }
}



/**
 * Checks whether or not the given username is in the
 * database, if so it checks if the given password is
 * the same password in the database for that user.
 * If the user doesn't exist or if the passwords don't
 * match up, it returns an error code (1 or 2). 
 * On success it returns 0.
 */
 
function confirmUser($username, $password){
   global $conn;
   /* Add slashes if necessary (for query) */
   if(!get_magic_quotes_gpc()) {
	$username = addslashes($username);
   }

   /* Verify that user is in database */
   $q = "SELECT password FROM users WHERE username = '$username'";
   $result = mysql_query($q,$conn);
   if(!$result || (mysql_numrows($result) < 1)){
      return 1; //Indicates username failure
   }

   /* Retrieve password from result, strip slashes */
   $dbarray = mysql_fetch_array($result);
   $dbarray['password']  = stripslashes($dbarray['password']);
   $password = stripslashes($password);

   /* Validate that password is correct */
   if($password == $dbarray['password']){

      return 0; //Success! Username and password confirmed
   }
   else{
      return 2; //Indicates password failure
   }
 }  
  
  
/**
 * checkLogin - Checks if the user has already previously
 * logged in, and a session with the user has already been
 * established. Also checks to see if user has been remembered.
 * If so, the database is queried to make sure of the user's 
 * authenticity. Returns true if the user has logged in.
 */
 
function checkLogin(){
   global $conn;
   /* Check if user has been remembered */
   if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
	 $_COOKIE['cookname'] = mysql_escape_string(filter_var(trim($_COOKIE['cookname']), FILTER_SANITIZE_STRING));
	 $_COOKIE['cookpass'] = mysql_escape_string(filter_var(trim($_COOKIE['cookpass']), FILTER_SANITIZE_STRING));
	 if(confirmUser($_COOKIE['cookname'], $_COOKIE['cookpass']) == 0){	
		
		$_SESSION['username'] = $_COOKIE['cookname'];
		$_SESSION['password'] = stripslashes($_COOKIE['cookpass']);
		
		$q = "SELECT * FROM users WHERE username ='".$_SESSION['username']."' AND password = '".$_SESSION['password']."' ";
		$result = mysql_query($q,$conn);
		$userinfo = mysql_fetch_array($result);
		
		$_SESSION['email'] = $userinfo['email'];
		$_SESSION['fname'] = $userinfo['first_name'];
		$_SESSION['lname'] = $userinfo['last_name'];
		$_SESSION['aem'] = $userinfo['aem'];
		$_SESSION['type'] = $userinfo['type'];
		$_SESSION['active'] = $userinfo['activated'];
		$_SESSION['id'] = $userinfo['id'];
		$_SESSION['user_id'] = $userinfo['id'];	//DASYGENIS ADDITION
		$_SESSION['telephone'] = $userinfo['telephone'];
		$_SESSION['academic_id'] = $userinfo['academicid'];
		$_SESSION['Login_expire'] = time();
		
		if(isset($_GET['entry'])){
			echo "<meta http-equiv='refresh' content='2;url=../".$_GET['entry']."'>";
		}

	 }
   }
   
   if(isset($_SESSION['username']) && isset($_SESSION['password'])){
   /* Username and password have been set */
      /* Confirm that username and password are valid */
      if(confirmUser($_SESSION['username'], $_SESSION['password']) != 0){
         /* Variables are incorrect, user not logged in */
         unset($_SESSION['username']);
         unset($_SESSION['password']);
		 unset($_SESSION['fname']);
		 unset($_SESSION['lname']);
		 unset($_SESSION['aem']);
		 unset($_SESSION['type']);
		 unset($_SESSION['active']);
		 unset($_SESSION['id']);
		 unset($_SESSION['telephone']);
		 unset($_SESSION['academic_id']);
         return false;
      }
      return true;
   }
   
   /*else{
   /* User not logged in 
      return false;
   }*/
}



/* Get user's ID. */

function getuid($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['id'];
}

/* Get user's type ( 1 admin and 0 for simple user ). */

function getype($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['type'];
}

/* Get user's activation status ( 1 activated, 0 not activated ). */

function getact($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['activated'];
}

/* Get user's phone number. */

function getphone($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['telephone'];
}

/* Get user's first name. */

function getfname($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['first_name'];
}

/* Get user's last name. */

function getlname($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['last_name'];
}

/* Get user's aem. */

function getaem($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['aem'];
}

/* Get user's academic ID. */

function getacademicid($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['academicid'];
}

/* Get user's E-mail. */

function getemail($usern){
	global $conn;
	$q = "SELECT * FROM users WHERE username='".$usern."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	return $row['email'];
}


/**
 * Checks to see if the user has submitted his
 * username and password through the login form,
 * if so, checks authenticity in database and
 * creates session.
 */
if(isset($_POST['sublogin'])){
	$user = mysql_escape_string(filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING));
	$pass = mysql_escape_string(filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING));
	
	if(($_POST['user'] == $user) && ($_POST['pass'] == $pass)){
		$_POST['user'] = $user;
		$_POST['pass'] = $pass;
   }else {
		die("<head><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'><center>You have enter invalid username and/or password.</center></div></div></body>");
   }
	
   /* Check that all fields were typed in */
   if(!$_POST['user'] || !$_POST['pass']){
      die('You didn\'t fill in a required field.');
   }
   /* Spruce up username, check length */
   $_POST['user'] = trim($_POST['user']);
   if(strlen($_POST['user']) > 25){
      die("Sorry, the username is longer than 25 characters, please shorten it.");
   }

   /* Checks that username is in database and password is correct */
   $md5pass = md5($_POST['pass']);
   $result = confirmUser($_POST['user'], $md5pass);

   /* Check error codes */
   if($result == 1){
      die('That username doesn\'t exist in our database.');
   }
   elseif($result == 2){
      die('Incorrect password, please try again.');
   }

   /* Username and password correct, register session variables */
   $activation = getact($_POST['user']);
   if($activation == 0){
	echo "<meta http-equiv='refresh' content='0;url=activation.php'>" ;
	die('You will have to activate your account to log in.');
   }
   $_SESSION['active'] = $activation;
   $_POST['user'] = stripslashes($_POST['user']);
   $_SESSION['username'] = $_POST['user'];
   $_SESSION['password'] = $md5pass;
   $_SESSION['email'] = getemail($_POST['user']);
   $_SESSION['id'] = getuid($_POST['user']);
   $_SESSION['fname'] = getfname($_POST['user']);
   $_SESSION['lname'] = getlname($_POST['user']);
   $_SESSION['aem'] = getaem($_POST['user']);
   $_SESSION['type'] = getype($_POST['user']);
   $_SESSION['telephone'] = getphone($_POST['user']);
   $_SESSION['academic_id'] = getacademicid($_POST['user']);
   $_SESSION['Login_expire'] = time();

   /**
    * This is the cool part: the user has requested that we remember that
    * he's logged in, so we set two cookies. One to hold his username,
    * and one to hold his md5 encrypted password. We set them both to
    * expire in 100 days. Now, next time he comes to our site, we will
    * log him in automatically.
    */
   if(isset($_POST['remember'])){
		setcookie("cookname", $_SESSION['username'], time()+60*60*24*100, "/");
		setcookie("cookpass", $_SESSION['password'], time()+60*60*24*100, "/");
   }

   /* Quick self-redirect to avoid resending data on refresh */
   
   
   if(isset($_POST['redirect']) && $_POST['redirect']=='igrades'){
		header( 'Location: ../igrades/index.php' ) ;
   }elseif(isset($_POST['redirect']) && $_POST['redirect']=='iexams'){
		header( 'Location: ../iexams/index.php' ) ;
   }elseif(isset($_POST['redirect']) && $_POST['redirect']=='ipresence'){
		header( 'Location: ../ipresence/index.php' ) ;
   }elseif(isset($_POST['redirect']) && $_POST['redirect']=='iexamsII'){
		header( 'Location: ../iexamsII/index.php' ) ;
   }elseif(isset($_POST['redirect']) && $_POST['redirect']=='administration'){
		header( 'Location: ../administration/index.php' ) ;
   }elseif(isset($_POST['redirect']) && $_POST['redirect']=='itasks'){
		header( 'Location: ../itasks/index.php' ) ;
   }elseif(isset($_POST['redirect']) && !empty($_POST['redirect'])){
		$cleandir=filter_var($_POST['redirect'], FILTER_SANITIZE_STRING);
		header( "Location: /$cleandir/" ) ;
		die();
   }else{
		header( 'Location: index.php' ) ;
   }
   
   return;
}

/* Sets the value of the logged_in variable, which can be used in your code */
$logged_in = checkLogin();





/* Misc Functions.*/




/* Show public announcements at login. */

function postpublicann(){
	global $conn;
	
	$query = "SELECT * FROM publicann ORDER BY id DESC ";
	$results = mysql_query($query,$conn);
	while($rows = mysql_fetch_array($results)){
		if($rows['announcement'] !='' && $rows['announcement'] != ' '){
			$dec_announcement = stripcslashes($rows['announcement']);
			echo "<div class='public_announcement' >".$dec_announcement."</div>";
		}
	}

}
if(isset($_REQUEST['request_global_ann'])){
postpublicann();
}




/* Show the current date. */

function show_comments_user(){
	global $conn;
	
	$query_comment = "SELECT public_comment FROM users WHERE id='".$_SESSION['id']."' ";
	$result_comment = mysql_query($query_comment,$conn);
	$row_comment = mysql_fetch_array($result_comment);
	if( !empty($row_comment['public_comment']) ){
	
		echo '<div id="announcments"><div id="header_ann"><center>Professor\'s Comment</center></div>';
		echo '<div id="content">'.$row_comment['public_comment'].'</div></div>';
	}

}




/* Show the current date. ajax */

function currentdate(){
	global $conn;
	$date=date('Y-m-d');
	$time=getdate();

	if($time['seconds']>30){
		$time['seconds']=30;
	}else{
		$time['seconds']='00';
	}
	//$time['hours']=$time['hours']-5; /* Adjust the time to the difference between server and country (in this case -5 hours). */
	
	$query = "SELECT * FROM schedules WHERE fin_date ='".$date."'";
	$res = mysql_query($query,$conn);
	$rows = mysql_fetch_array($res);
	if($rows['onoff'] == 1){
		if((($time['hours'] > $rows['start_hour']) || ($time['hours'] == $rows['start_hour'] && $time['minutes'] >= $rows['start_minute'])) && (($time['hours'] < $rows['fin_hour']) || ($time['hours'] == $rows['fin_hour'] && $time['minute'] <= $rows['fin_minute']))){
			if($time['minutes'] > $rows['minutes']){
				$olddatemin = $time['minutes'] - $rows['minutes'];
				$olddatehour = $time['hours'];
			}else{
				$tempmin = $rows['minutes'] - $time['minutes'];
				$olddatemin = 60 - $tempmin;
				$olddatehour = $time['hours'] - 1;
			}
			if($rows['groupschedule']==1){
				echo "<br /><center><font color=red>".$rows['title']."</font><br />";
				echo "<br />Ομαδική εξέταση.<br /></center>" ;
			}else{
				$q = "SELECT * FROM studentslots WHERE sch_date_id <= ".$rows['id'].$time['hours'].$time['minutes'].$time['seconds']." AND sch_date_id >= ".$rows['id'].$olddatehour.$olddatemin.$time['seconds']."";
				$result = mysql_query($q,$conn);
				$row=mysql_fetch_array($result);
				
				$q = "SELECT * FROM users WHERE id ='".$row['user_id']."'";
				$result = mysql_query($q,$conn);
				$row=mysql_fetch_array($result);
				
				echo "<br /><center><font color=red>".$rows['title']."</font>";
				if($row['first_name']){
					echo "<br />Φοιτητής/τρια: " . $row['first_name'] . " " . $row['last_name'] . "<br /></center>" ;
				}else{
					echo "</center>" ;
				}
			}
		}
	}

}


if(isset($_REQUEST['cd'])){

        if ($_REQUEST['cd']== "go"){ 
			currentdate();	
			//header('Location: index.php');
		}else{
			echo "Invalid request.<br><a href=index.php> Return to Main Page </a>";
		}

}


/* Show the current date before login. ajax */

function currentdateout(){
	global $conn;
	$date=date('Y-m-d');
	$time=getdate();

	if($time['seconds']>30){
		$time['seconds']=30;
	}else{
		$time['seconds']='00';
	}
	//$time['hours']=$time['hours']-5; /* Adgust the time to the difference between server and country (in this case -5 hours). */
	
	$query = "SELECT * FROM schedules WHERE fin_date ='".$date."'";
	$res = mysql_query($query,$conn);
	$rows = mysql_fetch_array($res);
	
if(((($time['hours'] > $rows['start_hour']) || ($time['hours'] == $rows['start_hour'] && $time['minutes'] >= $rows['start_minute'])) && (($time['hours'] < $rows['fin_hour']) || ($time['hours'] == $rows['fin_hour'] && $time['minute'] <= $rows['fin_minute']))) && $rows['onoff'] == 1){

	if($time['minutes'] > $rows['minutes']){
		$olddatemin = $time['minutes'] - $rows['minutes'];
		$olddatehour = $time['hours'];
	}else{
		$tempmin = $rows['minutes'] - $time['minutes'];
		$olddatemin = 60 - $tempmin;
		$olddatehour = $time['hours'] - 1;
	}
	
	if($rows['groupschedule']==1){
		echo "<br /><center><font color=red>".$rows['title']."</font><br />";
		echo "<br />Ομαδική εξέταση.<br /></center>" ;
	}else{
		$q = "SELECT * FROM studentslots WHERE sch_date_id <= ".$rows['id'].$time['hours'].$time['minutes'].$time['seconds']." AND sch_date_id >= ".$rows['id'].$olddatehour.$olddatemin.$time['seconds']."";
		$result = mysql_query($q,$conn);
		$row=mysql_fetch_array($result);
		
		$q = "SELECT * FROM users WHERE id ='".$row['user_id']."'";
		$result = mysql_query($q,$conn);
		$row=mysql_fetch_array($result);
		
		echo "<br /><center><font color=red>".$rows['title']."</font><br />";
		if($row['first_name']){
			echo "<br />Φοιτητής/τρια:  " . $row['first_name'] . " " . $row['last_name'] . "<br /></center>" ;
		}else{
			echo "<br />Δεν έχει κανείς ραντεβού αυτή τη στιγμή.<br /></center>" ;
		}
	}
}else{
	echo "<br />Δεν υπάρχει ενεργή εξέταση <br /><br />αυτή τη στιγμή.";
}

}


if(isset($_REQUEST['cdout'])){

        if ($_REQUEST['cdout']== "go"){ 
			currentdateout();	
			//header('Location: index.php');
		}else{
			echo "Invalid reqquest.<br><a href=index.php> Return to Main Page </a>";
		}

}



/* Check and activate user. */

function CheckActivation($activ){
	global $conn;
   
	$q = "UPDATE users SET activated='1' WHERE act_code=".$activ."";
	mysql_query($q,$conn);
	
}


if(isset($_REQUEST['actcode'])){

        if (($_REQUEST['actcode']) ==  strval(intval($_REQUEST['actcode']))){ 
			CheckActivation($_REQUEST['actcode']);
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
			echo "Ο λογαριασμός ενεργοποιήθικε.";
			$_SESSION['active']='1';
			echo "<meta http-equiv='refresh' content='0;url=index.php'>" ;
		}else{
			echo "ID is not valid. Please check your email carefully.<br><a href=index.php> Return to Main Page </a>";
		}

}



/*  Add new schedule. */
 
 function addNewService($filename,$en_date, $en_hour, $title, $day, $start_hour, $fin_hour, $start_minute, $fin_minute, $minutes, $seconds, $fin_date, $onoff, $gid, $ar_start, $ar_fin, $groupsch){
   global $conn;
   	if($en_date){
		if($ar_start > 0){
			$q = "INSERT INTO schedules (title, filename, day, start_hour, fin_hour, start_minute, fin_minute, minutes, seconds, fin_date, onoff, gid, en_date, en_hour, ar_start, ar_fin, groupschedule) VALUES ('$title', '$filename', '$day', '$start_hour', '$fin_hour', '$start_minute', '$fin_minute', '$minutes', '$seconds', '$fin_date', '$onoff', '$gid', '$en_date', '$en_hour', '$ar_start', '$ar_fin', '$groupsch')";
		}else{
			$q = "INSERT INTO schedules (title, filename, day, start_hour, fin_hour, start_minute, fin_minute, minutes, seconds, fin_date, onoff, gid, en_date, en_hour, groupschedule) VALUES ('$title', '$filename', '$day', '$start_hour', '$fin_hour', '$start_minute', '$fin_minute', '$minutes', '$seconds', '$fin_date', '$onoff', '$gid', '$en_date', '$en_hour', '$groupsch')";
		}
	}else{
		if($ar_start > 0){
			$q = "INSERT INTO schedules (title, filename, day, start_hour, fin_hour, start_minute, fin_minute, minutes, seconds, fin_date, onoff, gid, ar_start, ar_fin, groupschedule) VALUES ('$title', '$filename', '$day', '$start_hour', '$fin_hour', '$start_minute', '$fin_minute', '$minutes', '$seconds', '$fin_date', '$onoff', '$gid', '$ar_start', '$ar_fin', '$groupsch')";
		}else{
			$q = "INSERT INTO schedules (title, filename, day, start_hour, fin_hour, start_minute, fin_minute, minutes, seconds, fin_date, onoff, gid, groupschedule) VALUES ('$title', '$filename', '$day', '$start_hour', '$fin_hour', '$start_minute', '$fin_minute', '$minutes', '$seconds', '$fin_date', '$onoff', '$gid', '$groupsch')";
		}
	}
   return mysql_query($q,$conn);
}

if(isset($_POST['addsch'])){
	$title=mysql_escape_string(filter_var($_POST['title'], FILTER_SANITIZE_STRING));
	$filename=mysql_escape_string(filter_var($_POST['filename'], FILTER_SANITIZE_STRING));
	$day=mysql_escape_string(filter_var($_POST['day'], FILTER_SANITIZE_NUMBER_INT));
	$gid=mysql_escape_string(filter_var($_POST['gid'], FILTER_SANITIZE_NUMBER_INT));
	if($_POST['fin_date']){
		if(preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $_POST['fin_date'])){
			$fin_date=$_POST['fin_date'];
		}else {
			die('Wrong date form. Date must be the type of DD-MM-YYYY .');
		}
	}else{
		$fin_date=$_POST['fin_date'];
	}
	$start_minute=mysql_escape_string(filter_var($_POST['start_minute'], FILTER_SANITIZE_NUMBER_INT));
	$fin_minute=mysql_escape_string(filter_var($_POST['fin_minute'], FILTER_SANITIZE_NUMBER_INT));
	$start_hour=mysql_escape_string(filter_var($_POST['start_hour'], FILTER_SANITIZE_NUMBER_INT));
	$fin_hour=mysql_escape_string(filter_var($_POST['fin_hour'], FILTER_SANITIZE_NUMBER_INT));
	$minutes=mysql_escape_string(filter_var($_POST['minutes'], FILTER_SANITIZE_NUMBER_INT));
	$seconds=mysql_escape_string(filter_var($_POST['seconds'], FILTER_SANITIZE_NUMBER_INT));
	$onoff=mysql_escape_string(filter_var($_POST['onoff'], FILTER_SANITIZE_NUMBER_INT));
	if($_POST['en_date']){
		if(preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $_POST['en_date'])){
			$en_date=$_POST['en_date'];
		}else {
			die('Wrong date form. Date must be the type of DD-MM-YYYY .');
		}
	}else{
		$en_date=$_POST['en_date'];
	}
	if($_POST['en_hour']){
		$en_hour=mysql_escape_string(filter_var($_POST['en_hour'], FILTER_SANITIZE_NUMBER_INT));
	}else{
		$en_hour=$_POST['en_hour'];
	}
	if($_POST['groupsch']==1){
		$groupsch=1;
	}else{
		$groupsch=0;
	}
	$ar_start=mysql_escape_string(filter_var($_POST['ar_start'], FILTER_SANITIZE_NUMBER_INT));
	$ar_fin=mysql_escape_string(filter_var($_POST['ar_fin'], FILTER_SANITIZE_NUMBER_INT));
	if(($_POST['filename']==$filename) && ($_POST['en_date']==$en_date) && ($_POST['en_hour']==$en_hour) && ($_POST['title']==$title) && ($_POST['gid']==$gid) && ($_POST['start_hour']==$start_hour) && ($_POST['fin_hour']==$fin_hour) && ($_POST['start_minute']==$start_minute) && ($_POST['fin_minute']==$fin_minute) && ($_POST['minutes']==$minutes) && ($_POST['seconds']==$seconds) && ($_POST['fin_date']==$fin_date) && ($_POST['onoff']==$onoff)  && ($_POST['ar_start']==$ar_start)  && ($_POST['ar_fin']==$ar_fin) ){
		addNewService($filename,$en_date, $en_hour, $title, $day, $start_hour, $fin_hour, $start_minute, $fin_minute, $minutes, $seconds, $fin_date,$onoff, $gid, $ar_start, $ar_fin,$groupsch);
	}
	header( 'Location: index.php' ) ;

}



/* Edit schedule */

function EditService($title, $day, $start_hour, $start_minute, $fin_hour, $fin_minute, $fin_date, $gid, $id, $groupsch, $scheddurm, $scheddurs, $schedid){
   global $conn;
   
   $q = "UPDATE schedules SET title='$title', minutes='$scheddurm', seconds='$scheddurs', day='$day', start_hour='$start_hour', start_minute='$start_minute', fin_hour='$fin_hour', fin_minute='$fin_minute', fin_date='$fin_date', gid='$gid', groupschedule='$groupsch' WHERE id='$id'";
   return mysql_query($q,$conn);
}

if(isset($_POST['schedtitle'])){
	$schedtitle=mysql_escape_string(filter_var($_POST['schedtitle'], FILTER_SANITIZE_STRING));
	$schedday=mysql_escape_string(filter_var($_POST['schedday'], FILTER_SANITIZE_NUMBER_INT));
	$schedgid=mysql_escape_string(filter_var($_POST['schedgid'], FILTER_SANITIZE_NUMBER_INT));
	if(preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $_POST['schedfind'])){
		$schedfind=$_POST['schedfind'];
	}else {
		die('Wrong date form. Date must be the type of DD-MM-YYYY .');
	}
	$schedstrh=mysql_escape_string(filter_var($_POST['schedstrh'], FILTER_SANITIZE_NUMBER_INT));
	$schedstrm=mysql_escape_string(filter_var($_POST['schedstrm'], FILTER_SANITIZE_NUMBER_INT));
	$schedfinh=mysql_escape_string(filter_var($_POST['schedfinh'], FILTER_SANITIZE_NUMBER_INT));
	$schedfinm=mysql_escape_string(filter_var($_POST['schedfinm'], FILTER_SANITIZE_NUMBER_INT));
	$scheddurm=mysql_escape_string(filter_var($_POST['scheddurm'], FILTER_SANITIZE_NUMBER_INT));
	$scheddurs=mysql_escape_string(filter_var($_POST['scheddurs'], FILTER_SANITIZE_NUMBER_INT));
	$schedid=mysql_escape_string(filter_var($_POST['schedid'], FILTER_SANITIZE_NUMBER_INT));
	if($_POST['schgrouped']==1){
		$groupsch=1;
	}else{
		$groupsch=0;
	}

	if(($_POST['scheddurm']==$scheddurm) && ($_POST['scheddurs']==$scheddurs) && ($_POST['schedtitle']==$schedtitle) && ($_POST['schedday']==$schedday) && ($_POST['schedgid']==$schedgid) && ($_POST['schedstrh']==$schedstrh) && ($_POST['schedstrm']==$schedstrm) && ($_POST['schedfinh']==$schedfinh) && ($_POST['schedfinm']==$schedfinm) && ($_POST['schedfind']==$schedfind) && ($_POST['schedid']==$schedid) ){
		EditService( $schedtitle, $schedday, $schedstrh, $schedstrm, $schedfinh, $schedfinm, $schedfind, $schedgid, $schedid, $groupsch, $scheddurm, $scheddurs, $schedid);
	}

	header( 'Location: index.php' ) ;

}



/*  Add new schedule. */
 
 function duplicate_schedule($id){
	global $conn;
   
	$q = "SELECT * FROM schedules where id='".$id."'";
	$result = mysql_query($q,$conn);
	$row = mysql_fetch_array($result);

   	$q = "INSERT INTO schedules (title, filename, day, start_hour, fin_hour, start_minute, fin_minute, minutes, seconds, fin_date, onoff, gid, en_date, en_hour, ar_start, ar_fin, groupschedule) VALUES ('".$row['title']."_copy', '".$row['filename']."', '".$row['day']."', '".$row['start_hour']."', '".$row['fin_hour']."', '".$row['start_minute']."', '".$row['fin_minute']."', '".$row['minutes']."', '".$row['seconds']."', '".$row['fin_date']."', '".$row['onoff']."', '".$row['gid']."', '".$row['en_date']."', '".$row['en_hour']."', '".$row['ar_start']."', '".$row['ar_fin']."', '".$row['groupschedule']."')";
	return mysql_query($q,$conn);
}

if(isset($_POST['duplicate'])){
	$id=mysql_escape_string(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
	
	if(($_POST['id']==$id)){
		duplicate_schedule($id);
	}
	header( 'Location: index.php' ) ;

}



/* Enable/Disable schedule */

function endisable($id){
   	global $conn;
	$q = "SELECT * FROM schedules where id='".$id."'";
	$result = mysql_query($q,$conn);
	$row = mysql_fetch_array($result);
	//var_dump($row);
	
	if($row['onoff'] == 1){
		$query = "UPDATE schedules SET onoff=0 WHERE id='".$id."'";
		//echo "Disabled";
		return mysql_query($query,$conn); 
	}else {
		$query = "UPDATE schedules SET onoff=1 WHERE id='".$id."'";
		//echo "Enabled";
		return mysql_query($query,$conn); 
	}
}

if(isset($_POST['onoffsch'])){
	
	$sch_id=mysql_escape_string(filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT));

	endisable($sch_id);
	header( 'Location: index.php' ) ;
}



/* Edit auto enable schedule */

function EditAutoen($id, $en_date, $en_hour){
   global $conn;
   $q = "UPDATE schedules SET en_date='$en_date', en_hour='$en_hour' WHERE id='$id'";
   return mysql_query($q,$conn);
}

if(isset($_POST['scheddate'])){
	$en_hour=mysql_escape_string(filter_var($_POST['schedhour'], FILTER_SANITIZE_NUMBER_INT));
	if(preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $_POST['scheddate'])){
		$en_date=$_POST['scheddate'];
	}else {
		die('Wrong date form. Date must be the type of DD-MM-YYYY .');
	}
	$schedid=mysql_escape_string(filter_var($_POST['schedid'], FILTER_SANITIZE_NUMBER_INT));

	if(($en_hour == $_POST['schedhour']) && ($en_date == $_POST['scheddate']) && ($_POST['schedid']==$schedid) ){
		EditAutoen( $schedid, $en_date, $en_hour);
	}

	header( 'Location: index.php' ) ;

}



/* Edit auto recurring schedule */

function EditAutorec($id, $schedstartday, $schedfinday){
   global $conn;
   $q = "UPDATE schedules SET ar_start='$schedstartday', ar_fin='$schedfinday' WHERE id='$id'";
   return mysql_query($q,$conn);
}

if(isset($_POST['schedstartday'])){
	$schedfinday=mysql_escape_string(filter_var($_POST['schedfinday'], FILTER_SANITIZE_NUMBER_INT));
	$schedstartday=mysql_escape_string(filter_var($_POST['schedstartday'], FILTER_SANITIZE_NUMBER_INT));
	$schedid=mysql_escape_string(filter_var($_POST['schedid'], FILTER_SANITIZE_NUMBER_INT));

	if(($schedstartday == $_POST['schedstartday']) && ($schedfinday == $_POST['schedfinday']) && ($_POST['schedid']==$schedid) ){
		EditAutorec( $schedid, $schedstartday, $schedfinday);
	}

	header( 'Location: index.php' ) ;

}



/*  Remove all students from schedules. */

function Clearallschedules(){
   	global $conn;
	$q = "DELETE FROM studentslots ";

	return mysql_query($q,$conn);
}

if(isset($_REQUEST['Delete']) || isset($_REQUEST['Delete_x'])){
	Clearallschedules();
	header( 'Location: index.php' ) ;
}



/*  Remove all student from selected schedule. */

function Clearschedule($id){
   	global $conn;
	
	$query = "SELECT * FROM studentslots";
	$resultsch = mysql_query($query,$conn);
	while ($row = mysql_fetch_array($resultsch)){
	
		$length = strlen($id);
		$temp_sch_id = substr($row['sch_date_id'], 0, $length);

		if($temp_sch_id == $id){
			$q = "DELETE FROM studentslots  WHERE sch_date_id='".$row['sch_date_id']."'";
			mysql_query($q,$conn);
		}
	
	}
	
	return;

}

if(isset($_POST['emptysch']) || isset($_POST['emptysch_x'])){
	$id=mysql_escape_string(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
	if($id == $_POST['id']){
		Clearschedule($id);
	}
	header( 'Location: index.php' ) ;
	
}



/*  Remove selected student from schedule. */

function DeleteSelectedstud($sch_id){
   	global $conn;
	
	$q = "DELETE FROM studentslots  WHERE sch_date_id='".$sch_id."'";
	return mysql_query($q,$conn);


}

if(isset($_REQUEST['Deletestud']) || isset($_REQUEST['Deletestud_x'])){
	$sched_id=mysql_escape_string(filter_var($_POST['sched_id'], FILTER_SANITIZE_NUMBER_INT));
	
	DeleteSelectedstud($sched_id);
	header( 'Location: index.php' ) ;
	
}



/*  Show schedules table to the admin cp. */

function showcheduleadmin(){
	global $conn;
	echo "<center> <h2> Schedules. </h2> </center><br />";
	echo "<table border='1'><tr> <td style='min-width: 80px;'>Title</td> <td style='min-width: 20px;'>ID</td> <td style='min-width: 55px;'>Group ID</td> <td style='min-width: 80px;'>Day</td> <td style='min-width:  70px;'>Made at</td> <td style='min-width: 70px;'>Deadline</td><td style='min-width: 65px;'>Starts at</td><td style='min-width: 65px;'>Ends at</td><td style='min-width: 65px;'>Date Duration</td><td style='min-width: 65px;'>Ομαδικό</td></tr>";
	
	$query = "SELECT * FROM schedules order by id DESC";
	$resultsch = mysql_query($query,$conn);
	while ($rowsch = mysql_fetch_array($resultsch)){
		$timestamp = strtotime($rowsch['start_date']);
		$str_date = date("Y-m-d", $timestamp);
		
		$day=$rowsch['day'] + 1;
		$q = "SELECT * FROM days WHERE id='".$day."'";
		$res = mysql_query($q,$conn);
		$row = mysql_fetch_array($res);
		
		if($rowsch['start_minute'] < 10){
			$rowsch['start_minute']= '0'.$rowsch['start_minute'];
		}
		if($rowsch['fin_minute'] < 10){
			$rowsch['fin_minute']= '0'.$rowsch['fin_minute'];
		}

		echo "<tr id='sched".$rowsch['id']."'>";
		if($rowsch['groupschedule']==1){
			$group_sch_msg="<td>Ναι</td>";
		}else{
			$group_sch_msg="<td>Όχι</td>";
		}
		if(strlen($rowsch['minutes'])==1){
			$duration="0".$rowsch['minutes'];
		}else{
			$duration=$rowsch['minutes'];
		}
		if(strlen($rowsch['seconds'])==1){
			$duration.=":0".$rowsch['seconds'];
		}else{
			$duration.=":".$rowsch['seconds'];
		}
		echo "<td>".$rowsch['title']."</td><td>".$rowsch['id']."</td><td>".$rowsch['gid']."</td><td>".$row['title']."</td><td>".$str_date."</td><td>".$rowsch['fin_date']."</td><td>".$rowsch['start_hour']." : ".$rowsch['start_minute']."</td><td>".$rowsch['fin_hour']." : ".$rowsch['fin_minute']."</td>"."<td>".$duration."</td>".$group_sch_msg;
		echo "<td><form action='index.php' method='post'><input type='hidden' name='sch_id' value='".$rowsch['id']."'><input type='image' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/delico.png' width='20' height='20' name='delsch' value='Remove Schedule'></form></td>";
		echo "<td><img onclick='editsched(\"".$rowsch['title']."\",".$rowsch['id'].",".$rowsch['gid'].",\"".$row['id']."\",\"".$str_date."\",\"".$rowsch['fin_date']."\",".$rowsch['start_hour'].",".$rowsch['start_minute'].",".$rowsch['fin_hour'].",".$rowsch['fin_minute'].",".$rowsch['groupschedule'].",".$rowsch['minutes'].",".$rowsch['seconds']." )' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/editicon.jpg' width='20' height='20'></td>";
		echo "<td><form action='index.php' method='post'><input type='hidden' name='id' value='".$rowsch['id']."'><input type='image' src='images/recycle-binx60.png' width='23' height='23' name='emptysch' alt='Empty Schedule' value='Empty schedule' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;'></form></td>";
		if($rowsch['onoff'] == 0){
			echo "<td><form action='index.php' method='post'><input type='hidden' name='id' value='".$rowsch['id']."'><input type='submit' name='onoffsch' value='Enable'></form></td>";
		}else{
			echo "<td><form action='index.php' method='post'><input type='hidden' name='id' value='".$rowsch['id']."'><input type='submit' name='onoffsch' value='Disable'></form></td>";
		}
		echo "<td><form action='index.php' method='post'><input type='hidden' name='id' value='".$rowsch['id']."'><input type='submit' name='duplicate' value='Duplicate'></form></td>";
		echo "</tr>";

	}
	echo "</table>";
}



/*  Delete schedule. */

function DeleteSchedule($id){
   	global $conn;
	$q = "DELETE FROM schedules WHERE id='".$id."'";
	mysql_query($q,$conn);
	
	$q = "DELETE FROM studentslots  WHERE sch_date_id LIKE '".$id."%'";
	return mysql_query($q,$conn);
	
}

if(isset($_REQUEST['delsch']) || isset($_REQUEST['delsch_x'])){
	$sch_id=mysql_escape_string(filter_var($_POST['sch_id'], FILTER_SANITIZE_NUMBER_INT));

	DeleteSchedule($sch_id);
	header( 'Location: index.php' ) ;
}



/*  Show auto enable schedules table to the admin cp. */

function showautoenadmin(){
	global $conn;
	echo "<center> <h2> Auto Enable Schedules. </h2> </center><br />";
	echo "<table border='1'><tr> <td style='min-width: 100px;'>Title</td> <td style='min-width: 20px;'>ID</td> <td style='min-width: 100px;'>Enable date</td> <td style='min-width: 90px;'>Enable Hour</td></tr>";
	
	$query = "SELECT * FROM schedules ORDER BY ID";
	$resultsch = mysql_query($query,$conn);
	while ($rowsch = mysql_fetch_array($resultsch)){

		if($rowsch['en_date']){
			echo "<tr id='scheden".$rowsch['id']."'>";
			echo "<td>".$rowsch['title']."</td><td>".$rowsch['id']."</td><td>".$rowsch['en_date']."</td><td>".$rowsch['en_hour']."</td>";
			echo "<td><form action='index.php' method='post'><input type='hidden' name='sch_id' value='".$rowsch['id']."'><input type='image' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/delico.png' width='20' height='20' name='delautoen' value='Remove Autoen'></form></td>";
			echo "<td><img onclick='editautoen(\"".$rowsch['title']."\",".$rowsch['id'].",\"".$rowsch['en_date']."\",".$rowsch['en_hour']." )' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/editicon.jpg' width='20' height='20'></td>";
			echo "</tr>";
		}
		
	}
	echo "</table>";
}



/*  Show auto Recurring schedules table to the admin cp. */

function showautorecadmin(){
	global $conn;
	echo "<center> <h2> Auto Recurring Schedules. </h2> </center><br />";
	echo "<table border='1'><tr> <td style='min-width: 100px;'>Title</td> <td style='min-width: 100px;'>ID</td>   <td style='min-width: 20px;'>Day</td> <td style='min-width: 100px;'>Starting Day (Will become enabled at 00:01 of this day)</td> <td style='min-width: 100px;'>Disable day (Will become disabled at 00:01 of this day)</td></tr>";
	
	$query = "SELECT * FROM schedules";
	$resultsch = mysql_query($query,$conn);
	while ($rowsch = mysql_fetch_array($resultsch)){
		$start_day=getday($rowsch['ar_start']);
		$fin_day=getday($rowsch['ar_fin']);

		 $day=$rowsch['day'] + 1;
         $q = "SELECT * FROM days WHERE id='".$day."'";
         $res = mysql_query($q,$conn);
         $row = mysql_fetch_array($res);


		if($rowsch['ar_start']){
			echo "<tr id='schedrec".$rowsch['id']."'>";
			echo "<td>".$rowsch['title']."</td><td>".$rowsch['id']."</td><td>".$row['title']."</td><td>".$start_day."</td><td>".$fin_day."</td>";
			echo "<td><form action='index.php' method='post'><input type='hidden' name='sch_id' value='".$rowsch['id']."'><input type='image' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/delico.png' width='20' height='20' name='delautorec' value='Remove Autorec'></form></td>";
			echo "<td><img onclick='editautorec(\"".$rowsch['title']."\",".$rowsch['id'].",\"".$rowsch['ar_start']."\",".$rowsch['ar_fin']." )' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/editicon.jpg' width='20' height='20'></td>";
			echo "</tr>";
		}
		
	}
	echo "</table>";
}
function getday($day){
	if($day==1){
		$wordday='Δευτέρα';
	}elseif($day==2){
		$wordday='Τρίτη';
	}elseif($day==3){
		$wordday='Τετάρτη';
	}elseif($day==4){
		$wordday='Πέμπτη';
	}elseif($day==5){
		$wordday='Παρασκευή';
	}elseif($day==6){
		$wordday='Σάββατο';
	}elseif($day==7){
		$wordday='Κυριακή';
	}
	return $wordday;
}




/*  Remove autoenable from schedule. */

function RemoveAutoen($id){
   	global $conn;
	$q = "UPDATE schedules SET en_date=NULL, en_hour=NULL WHERE id='$id'";
	return mysql_query($q,$conn);
	
}

if(isset($_REQUEST['delautoen']) || isset($_REQUEST['delautoen_x'])){
	$sch_id=mysql_escape_string(filter_var($_POST['sch_id'], FILTER_SANITIZE_NUMBER_INT));

	RemoveAutoen($sch_id);
	header( 'Location: index.php' ) ;
}




/*  Remove autorecurring from schedule. */

function RemoveAutorec($id){
   	global $conn;
	$q = "UPDATE schedules SET ar_start=NULL, ar_fin=NULL WHERE id='$id'";
	return mysql_query($q,$conn);
	
}

if(isset($_REQUEST['delautorec']) || isset($_REQUEST['delautorec_x'])){
	$sch_id=mysql_escape_string(filter_var($_POST['sch_id'], FILTER_SANITIZE_NUMBER_INT));

	RemoveAutorec($sch_id);
	header( 'Location: index.php' ) ;
}




/*  Shows days table in admin cp. */

function showdays(){
	global $conn;
	echo "<div id='coverdayscomments'></div>";
	echo "<center> <h2> Days. </h2> </center><br />";
	echo "<table border='1'>";
	
	$query = "SELECT * FROM days";
	$resultsch = mysql_query($query,$conn);
	while ($row= mysql_fetch_array($resultsch)){
	
		if($row['onoff']==1){
			$sel1="selected='selected'";
			$sel2="";
		}else{
			$sel2="selected='selected'";
			$sel1="";
		}
	
	
	echo "<tr><form action='index.php' method='post'><span id='daycom".$row['id']."' class='dayscomments';><center>Input your comments/announcement : <br /><br /><textarea rows='4' cols='50' name='announcement'>".$row['announcement']."</textarea><br/><br/><a href='#' onclick='document.getElementById(\"daycom".$row['id']."\").style.display=\"none\";document.getElementById(\"coverdayscomments\").style.display=\"none\";event.preventDefault();event.returnValue=false;'>Click here when done.</a></center></span>";
	echo "<td style='min-width: 100px;'>Day Slot :</td> <td style='min-width: 20px;'>".$row['id']."<input  type='hidden' name='id' value='".$row['id']."'></td> ";
	echo "<td style='min-width: 100px;'>Title :</td> <td style='min-width: 100px;'><input type='text' name='title' maxlength='10' value='".$row['title']."'></td> ";
	echo "<td style='min-width: 100px;'>Show Day :</td> <td><select name='onoff'><option value='1' ".$sel1.">On</option><option value='0' ".$sel2.">Off</option></select> </td>";
	echo "<td><a href='#' onclick='document.getElementById(\"daycom".$row['id']."\").style.display=\"block\";document.getElementById(\"coverdayscomments\").style.display=\"block\";event.preventDefault();event.returnValue=false;'>Click to edit announcement</a></td>";
	echo "<td><input type='submit' name='daysgo' value='OK'></td>";
	echo "</td></form></tr>";
	
	}
	
	echo "</table>";
}



/*  Shows days' titles at selection in admin cp while adding schedule. */

function showdaysopt(){
	global $conn;

	$query = "SELECT * FROM days";
	$resultsch = mysql_query($query,$conn);
	while ($row= mysql_fetch_array($resultsch)){
		$day=$row['id'] - 1;
		echo "<option value='".$day."'>".$row['title']."</option>";
	}

}
/*  Shows days' titles at selection in admin cp while editting schedule. */
function showdaysoptedit(){
	global $conn;

	$query = "SELECT * FROM days";
	$resultsch = mysql_query($query,$conn);
	while ($row= mysql_fetch_array($resultsch)){
		$day=$row['id'];
		echo "<option value=\'".$day."\'>".$row['title']."</option>";
	}

}


/*  Edit day (title or on/off). */

function dayschange($announcement,$id,$title,$onoff){
   	global $conn;
	$q = "UPDATE days SET title='".$title."', onoff='".$onoff."', announcement='".$announcement."' WHERE id='".$id."' ";
	return mysql_query($q,$conn);
	
}

if(isset($_REQUEST['daysgo'])){
    $announcement = $_POST['announcement'];
	$id=mysql_escape_string(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
	$title=mysql_escape_string(filter_var($_POST['title'], FILTER_SANITIZE_STRING));
	$onoff=mysql_escape_string(filter_var($_POST['onoff'], FILTER_SANITIZE_STRING));
	if(($id==$_POST['id']) && ($title==$_POST['title']) && ($onoff==$_POST['onoff'])){
		dayschange($announcement,$id,$title,$onoff);
	}
	header( 'Location: index.php' ) ;
}



/* Shows the days tabs. */

function showdaystabs(){
	global $conn;
	$q = "SELECT * FROM days";
	$rslt = mysql_query($q,$conn);	
	global $totaltabs;
	while ($rows= mysql_fetch_array($rslt)){
			$totaltabs++;
	}
	
	echo "<div class='days'>";
	$dayisok ;
	$dayisfirst=1 ;
	$q = "SELECT * FROM days";
	$rslt = mysql_query($q,$conn);
	while ($rows= mysql_fetch_array($rslt)){
		if($rows['onoff']==1){
			$dayisok=checkforemptyday($rows['id']-1);
			if($dayisfirst==1 && $dayisok==1){
				$dayisfirst=0;
				echo "<div class='menutab' id='daymenu".$rows['id']."'><a href='javascript:#' onclick='menu(".$rows['id'].",".$totaltabs." )'>".$rows['title']."</a></div>";
			}else{
				echo "<div class='menutab' id='daymenu".$rows['id']."'><a href='javascript:#' onclick='menu(".$rows['id'].",".$totaltabs." )'>".$rows['title']."</a></div>";
			}
		}
	}
	
	echo "</div>";
}




/*  Shows Schedule files in admin cp. */

function showschfiles(){
	global $conn;
	echo "<center> <h2> Schedule files. </h2> </center><br />";
	echo "<table border='1'>";
	
	$query = "SELECT * FROM schedules";
	$resultsch = mysql_query($query,$conn);
	while ($row= mysql_fetch_array($resultsch)){
	
		
	echo "<tr><form action='index.php' method='post'><td style='min-width: 70px;color: #DDDDFF;'>".$row['title']."<input  type='hidden' name='id' value='".$row['id']."'></td> ";
	echo "<td style='min-width: 60px;'>File :</td> <td style='min-width: 200px;'><input type='text' name='schfilename' size='25' maxlength='150' value='".$row['filename']."'></td> ";
	echo "<td><input type='submit' name='schfilesgo' value='OK'></td>";
	echo "</td></form></tr>";
	
	}
	
	echo "</table>";
}


/*  Edit Schedule File. */

function schfilechange($id,$schfilename){
   	global $conn;
	$q = "UPDATE schedules SET filename='".$schfilename."' WHERE id='".$id."' ";
	return mysql_query($q,$conn);
	
}

if(isset($_REQUEST['schfilesgo'])){
	$id=mysql_escape_string(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
	$schfilename=mysql_escape_string(filter_var($_POST['schfilename'], FILTER_SANITIZE_STRING));
	if(($id==$_POST['id']) && ($title==$_POST['title']) && ($onoff==$_POST['onoff'])){
		schfilechange($id,$schfilename);
	}
	header( 'Location: index.php' ) ;
}




/* Add public announcement. */

function addpublicann($publicann){
	global $conn;
	
	$query = "INSERT INTO publicann (announcement) VALUES ('$publicann')";
	$results = mysql_query($query,$conn);
}

if(isset($_POST['addpublicann'])){
	$publicann=addslashes($_POST['publicanninput']);
	addpublicann($publicann);
	
	header( 'Location: index.php' ) ;

}




/*  Edit public announcement. */

function editpublicann($publicann,$id){
   	global $conn;
	
	$q = "UPDATE publicann SET announcement='".$publicann."' WHERE id='".$id."' ";
	return mysql_query($q,$conn);
}

if(isset($_POST['editpublicann'])){
	$publicann=addslashes($_POST['publicanneditinput']);
	$id=mysql_escape_string(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
	if( $id==$_POST['id'] ){
		editpublicann($publicann,$id);
	}
	header( 'Location: index.php' ) ;
}




/*  Delete public announcement. */

function deletepublicann($id){
   	global $conn;
	$q = "DELETE FROM publicann WHERE id='".$id."' ";
	return mysql_query($q,$conn);
}

if(isset($_POST['delpublicann']) || isset($_POST['delpublicann_x'])){
	$id=mysql_escape_string(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
	if( $id==$_POST['id'] ){
		deletepublicann($id);
	}
	header( 'Location: index.php' ) ;
}




/*  Show to admin the edit public announcement field. */

function editpublicannadmincp(){
   	global $conn;
	
	$q = "SELECT * FROM publicann";
	$results= mysql_query($q,$conn);
	
	$editpopups='';
	
	echo "<center> <h2> Public Announcements. </h2> </center><br />";
	echo "<table border='1'>";
	
	while ($rows = mysql_fetch_array($results)){
		$announcement_temp=stripcslashes($rows['announcement']);
		$announcement=htmlentities($announcement_temp, ENT_QUOTES, "UTF-8");
		echo "<tr><td style='min-width: 300px;'>".$announcement."</td> ";
		echo "<td><img onclick='showpopupeditpublcann(".$rows['id'].")' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/editicon.jpg' width='20' height='20'></td>";	
		echo "<td><form action='index.php' method='post'><input type='hidden' name='id' value='".$rows['id']."'><input type='image' name='delpublicann' value='Remove announcement' style='padding-top: 5px;padding-left: 2px;padding-right: 2px;' src='images/delico.png' width='20' height='20'></form></td>";	
		echo "</tr>";
		
		$editpopups.= "<center><div class='hidden' id='editanndiv_".$rows['id']."' ></div></center>";

	}
	
	echo "</table>";
	echo $editpopups;
}




/*  Show to admin the edit public announcement field. */

function editpublicannadmincppopup($id){
   	global $conn;
	
	$q = "SELECT * FROM publicann WHERE id='".$id."'";
	$results= mysql_query($q,$conn);
	$rows = mysql_fetch_array($results);
	
	$dec_announcement = stripcslashes($rows['announcement']);
		
	echo "<div class='public_announcement' id='public_announcement".$rows['id']."'>".$dec_announcement."</div><br/>";
	echo "<form class='inlineform' action='index.php' method='post' id='editann_".$rows['id']."'><input type='hidden' name='id' value='".$rows['id']."'>";
	echo "<textarea name='publicanneditinput' id='textareapublicann_".$rows['id']."' form='editann_".$rows['id']."' rows='7' cols='35'>".$dec_announcement."</textarea>";
	echo "<br/><input type='submit' name='editpublicann' onclick='hidepopupeditpublcann(".$rows['id'].")' value='Save'></form><button onclick='publicannpreview(".$rows['id'].")'>Preview</button><button onclick='hidepopupeditpublcann(".$rows['id'].")'>Cancel</button>";
}
if(isset($_GET['givetopopupeditpublicann'])){
	$id=mysql_escape_string(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
	if( $id==$_GET['id'] ){
		editpublicannadmincppopup($id);
	}
	return;
}




/* Delete expired schedules. */

function pruning(){
	global $conn;
	$today=date('Y-m-d');
	
	
	
	$query = "SELECT * FROM schedules";
	$resultsch = mysql_query($query,$conn);
	while ($rowsch = mysql_fetch_array($resultsch)){
		if($rowsch['fin_date'] < $today){
			if(isset($rowsch['ar_start']) && $rowsch['ar_start']>0){
			
				$todayy= substr($rowsch['fin_date'], 0, 4);
				$todaym= substr($rowsch['fin_date'], -5,2);
				$todayd= substr($rowsch['fin_date'], -2);
				$checkday=date("t");
				
				
				$temptodayd=$todayd+7;
				if($checkday < $temptodayd){
					$todayd=$checkday -$temptodayd;
					if($todaym==12){
						$todaym=1;
						$todayy=$todayy+1;
					}else{
						$todaym= $todaym+1;
					}
				}else{
					$todayd=$temptodayd;
				}
				
				$newdeadline = $todayy."-".$todaym."-".$todayd ;
				
				$q = "UPDATE schedules SET fin_date='".$newdeadline."' WHERE id='".$rowsch['id']."'";
				mysql_query($q,$conn);
				
				Clearschedule($rowsch['id']);
				
			}else{
				
				$q = "DELETE FROM schedules WHERE id='".$rowsch['id']."'";
				mysql_query($q,$conn);
			}
		}
	}
}



/* Delete old studentslots. */

function pruningslots(){
	global $conn;
	$query = "SELECT id,sch_date_id FROM studentslots";
	$results = mysql_query($query,$conn);
	while ($row = mysql_fetch_array($results)){
		$new=0;
		$inquery = "SELECT id FROM schedules";
		$resultsch = mysql_query($inquery,$conn);
		while ($rowsch = mysql_fetch_array($resultsch)){
			$length = strlen($rowsch['id']);
			$studentslot= substr($row['sch_date_id'], 0,$length);
			if( $studentslot == $rowsch['id'] ){
				$new=1;
			}
		}
		
		if($new==0){
			$inquery2 = "DELETE FROM studentslots WHERE id='".$row['id']."' ";
			mysql_query($inquery2,$conn);
		
		}
		
	}
}



/* Auto enable schedules */

function autoenable(){
	global $conn;
	$date=date('Y-m-d');
	$time=getdate();
	//$time['hours']=$time['hours']-5;
	
	$query = "SELECT * FROM schedules";
	$results = mysql_query($query,$conn);
	while ($row = mysql_fetch_array($results)){
		if(isset($row['en_date'])){
			if($row['onoff'] == 0){
				if($row['en_date'] == $date){
					if($row['en_hour'] <= $time['hours']){
						$q = "UPDATE schedules SET onoff='1' WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
						$q = "UPDATE schedules SET en_date=NULL, en_hour=NULL WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
					}
				}elseif($row['en_date'] < $date){
						$q = "UPDATE schedules SET onoff='1' WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
						$q = "UPDATE schedules SET en_date=NULL, en_hour=NULL WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
				}
			}else{
				$q = "UPDATE schedules SET en_date=NULL, en_hour=NULL WHERE id='".$row['id']."'";
				mysql_query($q,$conn);
			}
		}
	}
}



/* Auto recurring schedules */

function autorecurring(){
	global $conn;
	$today=date('N');

	$query = "SELECT id,onoff,ar_start,ar_fin FROM schedules";
	$results = mysql_query($query,$conn);
	while ($row = mysql_fetch_array($results)){
		if($row['ar_start'] > 0){
			if($row['ar_start'] < $row['ar_fin']){	
				if(($row['ar_start'] <= $today) && ($today < $row['ar_fin'])){
					if($row['onoff'] == 0){
						$q = "UPDATE schedules SET onoff='1' WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
					}
				}else{
					if($row['onoff'] == 1){
						$q = "UPDATE schedules SET onoff='0' WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
					}
				}
			}else{
				if(($row['ar_fin'] <= $today) && ($today < $row['ar_start'])){
					if($row['onoff'] == 1){
						$q = "UPDATE schedules SET onoff='0' WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
					}
				}else{
					if($row['onoff'] == 0){
						$q = "UPDATE schedules SET onoff='1' WHERE id='".$row['id']."'";
						mysql_query($q,$conn);
					}
				}
			}
		}
	}
}



/* Register user into selected schedule slot. */

function RegisterNewDate($date_id, $user_id, $gid){
	global $conn;
	$query_check= "SELECT * FROM studentslots WHERE sch_date_id='".$date_id."' ";
	$results_check = mysql_query($query_check,$conn);
	$num_rows=mysql_num_rows($results_check);
	if($num_rows==0){
		$q = "DELETE FROM studentslots WHERE user_id='".$user_id."' AND gid='".$gid."' ";
		mysql_query($q,$conn);
		
		$query = "INSERT INTO studentslots (user_id, sch_date_id, gid) VALUES ($user_id, $date_id, $gid) ";
		return  mysql_query($query,$conn);
	}

}

if(isset($_POST['datereg']) || isset($_POST['datereg_x'])){
	$date_id=mysql_escape_string(filter_var($_POST['date_id'], FILTER_SANITIZE_NUMBER_INT));
	$user_id=mysql_escape_string(filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT));
	$gid=mysql_escape_string(filter_var($_POST['gid'], FILTER_SANITIZE_NUMBER_INT));
	if(($date_id == $_POST['date_id']) && ($user_id == $_POST['user_id']) && ($gid == $_POST['gid'])){
		RegisterNewDate($_POST['date_id'], $_POST['user_id'], $_POST['gid']);
	}
	header( 'Location: index.php' ) ;
}



/* Show schedules. 1st function makes the divs and calls in loop the other 2 functions to cover all days and schedules.*/

function showschedulesperday(){
	$thefirst=1;
	$i=0;
	global $conn;
	
	$query = "SELECT * FROM days";
	$res = mysql_query($query,$conn);
	while($row = mysql_fetch_array($res)){
	
		$styling="style='display:none;'";
		$xronid= $i + 1;
		$fullday=checkforemptyday($i);
		if($row['onoff']==1){
			if($fullday==1 && $thefirst==1){
				$styling=" ";
				$thefirst=0;
			}
			
			echo "<div id='xron".$xronid."' ".$styling."> ";
			if(isset($row['announcement']) && ($row['announcement'] != "") && ($row['announcement'] != " ") ){
				echo "<center><div class='daysannouncement'>".$row['announcement']."</div></center>";
			}
			CheckService($i); 
			echo "</div>";
		}
	$i++;
	}
	
	if( $_SESSION['active']=='0'){
		echo "<center><b>You need to activate your account in order to register any dates !<br /> E-mail for activation has been sent <br />to your mail based on your aem ( st0-AEM-@icte.gr ).</b></center><br />";
	}
	if( $_SESSION['active']=='2'){
		echo "<center><b>Your account has been marked as retired by the administration.<br/>You have no longer access to the website's functionalities.</b></center><br />";
	}
	
	
}

/* Show schedules. 2nd function makes the tables for each schedule.*/

function CheckService($schnumber){
	global $conn;
	$time=getdate();
	$today=date('Y-m-d');

	//$time['hours']=$time['hours']-5;

	$query = "SELECT * FROM schedules WHERE day ='".$schnumber."' ORDER BY fin_date,id ASC";
	$res = mysql_query($query,$conn);
	
	while ($rows = mysql_fetch_array($res)){

		if($rows['onoff'] == 1){
			echo "<center> <h2>".$rows['title']."</h2> <form action='printschedule.php' method='post' class='inlineform'><input type='hidden' name='schedule_id' value='".$rows['id']."'><input title='Εκτύπωση' type='image' src='images/print.png' width='31' height='30' name='printok' value='Print Schedule'></form>";
			if($_SESSION['type']==1){
				echo " <span class='clickableimg' onclick='showmailtoallform(".$rows['id'].",\"".$rows['title']."\");'><img border='0' title='Μαζική Αποστολή E-mail' src='images/mails.png' alt='Send mail to users in schedule' width='40' height='40'></span> " ;
				echo "<div id='formspammail_".$rows['id']."' class='formspammail'></div>";

				echo " <span class='clickableimg' onclick='showsmstoallform(".$rows['id'].",\"".$rows['title']."\");'><img border='0' title='Μαζική αποστολή SMS' src='images/sms-40.png' alt='Send sms to users in schedule' width='40' height='40'></span> " ;
				echo "<div id='formspamsms_".$rows['id']."' class='formspammail'></div>";
				
				echo " <span class='clickableimg' onclick='showicspopup(".$rows['id'].",\"".$rows['title']."\");'><img border='0' title='Αποστολή αρχείου ICS υπενθύμισης ημερολογίου' src='images/ical-mail-40.png' alt='Send e-mail with ICalc event of this schedule' width='40' height='40'></span> " ;
				
			}
			echo "</center><br />";
			if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
				if(isset($rows['filename']) && ($rows['filename'] != "") && ($rows['filename'] != " ") ){
					echo "<center><a href='functions.php?file=".$rows['filename']."'>".$rows['filename']." </a></center><br/><br/>";
				}
			}
			echo "<table id='datetable' border='1'><tr><td>A/A</td> <td id='timetd'>Time</td> <td id='nametd'>Name</td> <td id='aemtd'>A.E.M.</td></tr>";
			$lock=0;
			$firsthourpassed=0;
			$de = 0;
			$min = "00";
			$tim = "00";
			$finalleftover=0;
			$stopschdl=0;
			$countrow=1;
			$ids='';
			if($rows['start_minute'] < 10){
				$groupmin= "0".$rows['start_minute'];
			}else {
				$groupmin= $rows['start_minute'];
			}
			if($rows['seconds'] == 0){
				$groupsec= "0".$rows['seconds'];
			}else {
				$groupsec= $rows['seconds'];
			}
			$grouptime=$rows['start_hour'].":".$groupmin.":".$groupsec;
				
			if($rows['fin_date'] == $today){
				if((($time['hours'] > $rows['start_hour']) || ($time['hours'] == $rows['start_hour'] && $time['minutes'] >= $rows['start_minute'])) && (($time['hours'] < $rows['fin_hour']) || ($time['hours'] == $rows['fin_hour'] && $time['minute'] <= $rows['fin_minute']))){
					$lock=1;
				}
			}
			
			for ($j = $rows['start_hour']; $j <= $rows['fin_hour']; $j++) {
				for ($i = 0; $i < 60; $i= $i+$rows['minutes']) {
				
					if( ($j == $rows['fin_hour']) && ($rows['fin_minute'] <= $i) ){
							$stopschdl=1;
					}
					
					if($stopschdl==0){
						if($i >0){
							$finalleftover= 60 - $i;
						}
						if($firsthourpassed==1 && $i==0 && $finalleftover>0){
							if($finalleftover < $rows['minutes']){
								$i = $rows['minutes']-$finalleftover;
							}
						}
						
						if($firsthourpassed==0){
							$i=$rows['start_minute'];
							$firsthourpassed=1;
						}
						
						if($i < 10){
							$min= "0".$i;
						}else {
							$min= $i;
						}
					
						if($rows['seconds'] == '30'){
							if($de == 1){
								$tim = "30";
								$de=0;
								$i++;
							}else {
								$tim = "00";
								$de=1;
							}
						}
						
						$q = "SELECT * FROM studentslots WHERE sch_date_id ='".$rows['id'].$j.$min.$tim."'";
						$result = mysql_query($q,$conn);
						$row=mysql_fetch_array($result);
						$date_id=$rows['id'].$j.$min.$tim ;
						if(isset($row['user_id'])){
						
							$q = "SELECT * FROM users WHERE id ='".$row['user_id']."'";
							$result = mysql_query($q,$conn);
							$row=mysql_fetch_array($result);
							
							$ids .= $row['id']." ";
							
							if($rows['groupschedule']==1){
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$grouptime." </td> ";
							}else{
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$j.":".$min.":".$tim." </td> "; 
							}
							if($_SESSION['type']==1){
								echo "<td id='nametd' class='clickforinfo' onclick='getuserinfo(" . $row['aem'] . ");'>" . $row['first_name'] . " " . $row['last_name'] . " </td><td id='aemtd'> " . $row['aem'] . " </td>" ;
							}else{
								echo "<td id='nametd'>" . $row['first_name'] . " " . $row['last_name'] . " </td><td id='aemtd'> " . $row['aem'] . " </td>" ;
							}
						}else{
							if($rows['groupschedule']==1){
								echo "<tr><td>".$countrow.". <td id='timetd'>".$grouptime." </td> <td id='nametd'> </td><td id='aemtd'> </td>" ;
							}else{
								echo "<tr><td>".$countrow.". <td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'> </td><td id='aemtd'> </td>" ;
							}
						}
						$countrow++;
						if(!isset($row['id']) && $_SESSION['active']=='1' && ($_SESSION['type']==1 || $lock==0)){
							echo "<td><form action='index.php' method='post'><input type='hidden' name='date_id' value='".$date_id."'><input type='hidden' name='user_id' value='".$_SESSION['id']."'><input type='hidden' name='gid' value='".$rows['gid']."'><input title='Δέσμευση Χρονοθυρίδας' type='image' src='images/tick.png' width='31' height='30' name='datereg' value='Register Time'></form></td>";
						}
						if($_SESSION['type']==1 || ($row['id']==$_SESSION['id'] && $lock==0)){
							$date = get_schedule_date($rows['title'], $rows['fin_date']);
							$start_date = new DateTime($date." ".$j.":".$min,new DateTimeZone('Europe/Athens'));
							$start_date->setTimezone(new DateTimeZone('UTC'));
							$start_date_formated = $start_date->format('Ymd\THis\Z');
							$fin_time = get_next_schedule_slot($min, $rows['minutes'], $j);
							$end_date = new DateTime($date." ".$fin_time,new DateTimeZone('Europe/Athens'));
							$end_date->setTimezone(new DateTimeZone('UTC'));
							$end_date_formated = $end_date->format('Ymd\THis\Z');
							echo "<td><form action='index.php' method='post'><input type='hidden' name='sched_id' value='".$date_id."'><input title='Απομάκρυνση Δέσμευσης Χρονοθυρίδας' type='image' class='delicon' src='images/delico.png' width='20' height='20' name='Deletestud' value='Clean Date'></form></td>";
							echo "<td><input title='Αποστολή αρχείου ICS υπενθύμισης ημερολογίου' type='image' class='delicon' src='images/ical-mail-40.png' width='20' height='20' name='icalc_stud' value='Icalc Event' onclick='showicspopup_stud(\"".$rows['id']."-".$j.":".$min."\")'></td>";
							echo "<td><a href='http://www.google.com/calendar/event?action=TEMPLATE&text=".$rows['title']."
&dates=".$start_date_formated."/".$end_date_formated."
&ctz=Europe/Athens&details=Δεσμευμένη Χρονοθυρίδα για το γεγονός ".$rows['title']." για το φοιτητή ".$row['fname']." ".$row['lname']." με ΑΕΜ ".$row['aem']." στο Εργαστήριο Ψηφιακών Συστημάτων και Αρχιτεκτονικής Υπολογιστών Διασταύρωση Καραμανλή και Λοϊζου στα Εργαστήρια Μηχανολόγων (http://arch.icte.uowm.gr/address.php) .
&location=40.301478, 21.800931&trp=false&sprop=&sprop=name:'target='_blank' rel='nofollow'><img src='images/ical-40.png' class='delicon' title='Προσθήκη γεγονότος ical στο ημερολόγιο' width='25' height='25' /></a></td>";
						}
						echo "</tr>";
						
						$tempmin = $rows['minutes'] + $i; 
						if($tempmin >= 60){
							$tempmin = $tempmin - 60 ;
						}
						if( ($j == $rows['fin_hour']) && ($tempmin >= $rows['fin_minute']) ) {
							$i=61;
						}
						
					}
				}

			}
 
		echo "</table><br />";
		echo "<input type='hidden' id='sch_ids_".$rows['id']."' value='".$ids."'>";
		}

	}

}


/* Show schedules. 3rd function checks if the day has any schedules not.*/

function checkforemptyday($daynumber){
	global $conn;
	$show=0;
	$query = "SELECT * FROM schedules WHERE day ='".$daynumber."'";
	$res = mysql_query($query,$conn);
	while ($rows = mysql_fetch_array($res)){
		if($rows['onoff'] == 1){
			$show=1;
		}
	}
	return $show;
}


/* Edit user's details.*/

function Change($pass, $nicko, $fnameo, $lnameo, $whoo, $id, $telephone, $email){
   global $conn;
   $nick = stripslashes($nicko); 
   $fname = stripslashes($fnameo);   
   $lname = stripslashes($lnameo);   
   $who = stripslashes($whoo);
   $email = stripslashes($email);
	//var_dump($pass, $nicko, $fnameo, $lnameo, $whoo, $id, $telephone, $email);die();
    	//string(32) "e5678fa3286ac5e19a8fdd6bb2de6ecc" string(5) "admin" string(20) "Συστήματος" string(24) "Διαχειριστής" string(5) "admin" string(1) "2" string(10) "6936386123" string(20) "mdasygenis@gmail.com"
   
	//DASYGENIS, modified the selection procedure
	//original was only email, but I found out that there are people with the same email and thus we bypass this by selecting the username also
   $query = "SELECT COUNT(*) FROM users WHERE email ='".$email."'";
	$res = mysql_query($query,$conn);
	$row = mysql_fetch_array($res);
	if ($row['COUNT(*)']>1) {
	 //echo("fixxing double email on database");
	 $query = "SELECT COUNT(*) FROM users WHERE email ='".$email."' AND username = '".$nick."'";
	}
   $res = mysql_query($query,$conn);
   $row = mysql_fetch_array($res);	
	//var_dump($row);die();
	//this means that if the query does not match, thus we have either different email or nick?
	//was  if($row['COUNT(*)'] == 0){    // but I think it is wrong
   if($row['COUNT(*)'] == 1){
	   $q = "UPDATE users SET password='".$pass."', username='".$nick."', email='".$email."', first_name='".$fname."', last_name='".$lname."', telephone='".$telephone."'  WHERE id='".$id."'";
	   return mysql_query($q,$conn);
   }else{
	   return 0;
   }
}

if(isset($_POST['change'])){
	if(strlen($_POST['username']) <= 25){
		$username=mysql_escape_string(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
		$email=mysql_escape_string(filter_var($_POST['email'], FILTER_SANITIZE_STRING));
		$firstname=mysql_escape_string(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING));
		$lastname=mysql_escape_string(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING));
		$pass=mysql_escape_string(filter_var($_POST['pass'], FILTER_SANITIZE_STRING));
		$telephone = mysql_escape_string(filter_var(trim($_POST['telephone']), FILTER_SANITIZE_NUMBER_INT));
		if(($username == $_POST['username']) && ($firstname == $_POST['firstname']) && ($lastname == $_POST['lastname']) && ($pass == $_POST['pass']) && ($telephone == $_POST['telephone']) ){
			
			if(strlen($_POST['pass']) <= 25){
				$password = md5(stripslashes($pass));
			}else {
				$password = stripslashes($pass);
			}
		
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $email;
			$_SESSION['password'] = $password;
			$_SESSION['fname'] = $firstname;
			$_SESSION['lname'] = $lastname;
			$_SESSION['telephone'] = $telephone;
	   
			Change($password, $_POST['username'], $_POST['firstname'], $_POST['lastname'], $_SESSION['username'], $_POST['user_id'], $telephone, $email);
		}
		
		header( 'Location: index.php' ) ;
	}else{
		echo "Username too long. Make sure it's less than 25 characters.";
	}
}

/* Edit user's academic ID.*/

function edit_academic_id($academic_id,$userid,$remote_addr){
	global $conn;

	$query_user = "UPDATE users SET academicid='".$academic_id."'  WHERE id='".$userid."'";
	if(mysql_query($query_user,$conn)){
		$_SESSION['academic_id'] = $academic_id;
		$query_log = "INSERT INTO accademicid_log (userid, academicid, Remote_ADDR) VALUES ('$userid', '$academic_id', '$remote_addr')";
		return mysql_query($query_log,$conn);
	}
}

if(isset($_POST['change_academic_id'])){
	if(strlen($_POST['academic_id']) <= 12){
		$academic_id=mysql_escape_string(filter_var($_POST['academic_id'], FILTER_SANITIZE_NUMBER_INT));
		$userid=mysql_escape_string(filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT));
		$remote_addr=$_SERVER['REMOTE_ADDR'];
		
		if( ($academic_id == $_POST['academic_id']) && ($userid == $_POST['user_id']) ){
			edit_academic_id($academic_id,$userid,$remote_addr);
		}
	}
}


/* Resend the activation e-mail.*/

function resendactmail($aem){
	global $conn;
	$q = "SELECT * FROM users WHERE aem='".$aem."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	$activcode=$row['act_code'];

	
	$to = $row['email'];
	$subject = "Schedule Manager Registration";
	$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at index.php . You can complete registration by clicking the following link:\r https://arch.icte.uowm.gr/schedule/functions.php?actcode=".$activcode." \r\r If this is an error, ignore this email and you will be removed from our mailing list.";
	$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion();
	mail($to, $subject, $message, $headers);
}

if(isset($_POST['reactivmail'])){
	$user_aem=mysql_escape_string(filter_var($_POST['user_aem'], FILTER_SANITIZE_NUMBER_INT));
	if($user_aem == $_POST['user_aem']){
		resendactmail($user_aem);
	}
	header( 'Location: index.php' ) ;
}


/* Send password reset e-mail.*/

function resetpassmail($aem, $username){
	global $conn;
	$q = "SELECT * FROM users WHERE aem='".$aem."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	if($username != $row['username']){die("<head><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'><center>Username does not match AEM.</center></div></div></body>");}
	$newpass = mt_rand(10000000, 99999999);
	$md5pass = md5($newpass);
	$query = "UPDATE users SET password='".$md5pass."' WHERE aem='".$aem."' ";
	mysql_query($query,$conn);
	
	$to = $row['email'];
	$subject = "Laboratory of Digital Systems and Computer Architecture Online manager - Reset Password";
	$message = "Password reset \r\n You, or someone using your A.E.M and username, has request a password reset . \r\n You can change this password into something you will remember through the user control panel once you log in.  \r\n  \r\n New Password : ".$newpass ;
	$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion();
	mail($to, $subject, $message, $headers);
}

if(isset($_POST['respass'])){
	$user_name = mysql_escape_string(filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING));
	$user_aem=mysql_escape_string(filter_var($_POST['aem'], FILTER_SANITIZE_NUMBER_INT));
	if($user_aem == $_POST['aem'] && $user_name == $_POST['username']){
		resetpassmail($user_aem, $user_name);
	}
	header( 'Location: index.php' ) ;
}


/* Send username reminder e-mail.*/

function remindusernamemail($aem){
	global $conn;
	$q = "SELECT * FROM users WHERE aem='".$aem."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	
	$to = $row['email'];
	$subject = "Laboratory of Digital Systems and Computer Architecture Online manager - Remind Username";
	$message = "Remind Username \r\n You, or someone using your A.E.M, has request a reminding of your username. \r\n In case this was not your request, you can safely ignore or delete this e-mail.  \r\n  \r\n Username : ".$row['username'] ;
	$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion();
	mail($to, $subject, $message, $headers);
}

if(isset($_POST['usernameremind'])){

	$user_aem=mysql_escape_string(filter_var($_POST['aemremind'], FILTER_SANITIZE_NUMBER_INT));
	if($user_aem == $_POST['aemremind']){
		remindusernamemail($user_aem);
	}
	header( 'Location: index.php' ) ;
}



/* Send e-mails to all registered to schedule .*/

function sendmailtoallschedule($sch_id, $message){
	global $conn;
	$q_sch = "SELECT * FROM schedules WHERE id='".$sch_id."' ";
	$result_sch = mysql_query($q_sch,$conn);
	$row_sch = mysql_fetch_array($result_sch);
	
	$q_slots = "SELECT * FROM studentslots";
	$result_slots = mysql_query($q_slots,$conn);
	while ($row_slots=mysql_fetch_array($result_slots)){
	
		$length = strlen($sch_id);
		$temp_sch_id = substr($row_slots['sch_date_id'], 0, $length);
		
		if( $sch_id == $temp_sch_id ){
		
			$q_user = "SELECT * FROM users WHERE id ='".$row_slots['user_id']."'";
			$result_user = mysql_query($q_user,$conn);
			$row_user=mysql_fetch_array($result_user);
			
			$aem=$row_user['aem'];
			
			$content = fopen('emailsignature.txt',r);
			$data = fread($content, filesize('emailsignature.txt'));
			fclose($content);
			
			$to = $row_user['email'];
			$subject = "i-Timeslots Announcement";
			$fin_message = "Μήνυμα για τη χρονοθυρίδα ".$row_sch['title']." :\r\n\r\n";
			$fin_message .= $message ;
			$fin_message .= $data ;
			$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'Content-type: text; charset=UTF-8;' . "\r\n".'X-Mailer: PHP/'.phpversion();
			mail($to, $subject, $fin_message, $headers);
		}
	}
}

if(isset($_POST['spam_ann_mail'])){
	$sch_id=mysql_escape_string(filter_var($_POST['sch_id'], FILTER_SANITIZE_NUMBER_INT));
	if($sch_id == $_POST['sch_id']){
		sendmailtoallschedule($sch_id, $_POST['message']);
	}
	header( 'Location: index.php' ) ;
}



/* Return User Info to admin.*/

function getuserinfoonclick($aem){
	global $conn;
	$q = "SELECT * FROM users WHERE aem='".$aem."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	
	echo "User : ".$row['last_name']." ".$row['first_name']."<br />AEM : ".$aem."<br />Phone number : ".$row['telephone']."<br />E-mail : ".$row['email'] ;
}

if(isset($_GET['get_user_info']) && $_GET['get_user_info']=='1'){

	$aem=mysql_escape_string(filter_var($_GET['aem'], FILTER_SANITIZE_NUMBER_INT));
	if($aem == $_GET['aem']){
		getuserinfoonclick($aem);
	}

}



/* Return User phone to admin.*/

function getuserphoneonclick($id){
	global $conn;
	$q = "SELECT * FROM users WHERE id='".$id."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	
	if( preg_match("/^69[0-9]{8}$/",$row['telephone']) ){
		echo $row['telephone'];
	}else{
		echo '-1';
	}
}

if(isset($_GET['get_user_phone']) && $_GET['get_user_phone']=='1'){
	$id_filtered = mysql_escape_string(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));

	if($id_filtered == $_GET['id'] && $id_filtered!=NULL && $id_filtered!='' && $id_filtered!=' '){
		getuserphoneonclick($id_filtered);
	}else{
		echo '-1';
	}


}



/* Register Functions. Sanitize happens as soons as they sent the $_POST[] so it's not needed again here.*/



/* Check if username is taken.*/

function usernameTaken($username){
   global $conn;
   if(!get_magic_quotes_gpc()){
      $username = addslashes($username);
   }
   $q = "select username from users where username = '$username'";
   $result = mysql_query($q,$conn);
   return (mysql_numrows($result) > 0);
}

/* Check if A.E.M is taken.*/

function aemTaken($aem){
   global $conn;
   if(!get_magic_quotes_gpc()){
      $aem = addslashes($aem);
   }
   $q = "select aem from users where aem = '$aem'";
   $result = mysql_query($q,$conn);
   return (mysql_numrows($result) > 0);
}

/* Check if username is taken.*/

function addNewUser($username1, $password, $first_name1, $last_name1, $aem1, $ran, $telephone,$department,$email){
   $username=stripslashes($username1); 
   $first_name=stripslashes($first_name1);
   $last_name=stripslashes($last_name1); 
   $aem=stripslashes($aem1);
   //dxedit start
   //Thanks for providing me code for the e-mail funtion
   //You should have added that from the start though -.-
   
   global $conn;
   $q = "INSERT INTO users (username, password, first_name, last_name, aem, email, telephone, act_code, departmentid) VALUES ('$username', '$password', '$first_name', '$last_name', '$aem', '$email', '$telephone', '$ran', '$department')";
   //dxedit end
   return mysql_query($q,$conn);

}

/* Send the registration mail.*/

function registrationMail($aem){
	global $conn;
	$q = "SELECT * FROM users WHERE aem='".$aem."' ";
	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	$activcode=$row['act_code'];

	
	$to = $row['email'];
	$subject = "Schedule Manager Registration";
	$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at index.php . You can complete registration by clicking the following link:\r https://arch.icte.uowm.gr/schedule/functions.php?actcode=".$_SESSION['ran']."\r\r If this is an error, ignore this email and you will be removed from our mailing list.";
	$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion();
	mail($to, $subject, $message, $headers);
}




/* Show pdf file.*/

function showpdf($pdf_file){


$path_to_pdf_files = "../../files";  // Balte edo to swsto asfales path

$pdf_location = "$path_to_pdf_files/$pdf_file";
if (!file_exists($pdf_location)) {
	die("The file you requested could not be found.");
}


header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=$pdf_file");
$filesize = filesize($pdf_location);
header("Content-Length: $filesize");

readfile($pdf_location);
}

if(isset($_GET['file'])){
	if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
		$pdf_file = basename($_GET['file'], ".pdf") . ".pdf";
		showpdf($pdf_file);
	}else{
		die("You are not logged in!");
	}
}

class ICS {
	var $data="";
	var $name;
	function ICS_update($start,$end,$name,$description,$location) {
		$this->name = $name;
		$this->data .= "BEGIN:VEVENT\r\n".
			"ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;CN=\"".$_SESSION['fname']." ".$_SESSION['lname']."\";RSVP=TRUE:mailto:".$_SESSION['email']."\r\n".
			"ORGANIZER;CN=arch.icte.uowm.gr-ISchedules:mailto:noreply@spam.vlsi.gr\r\n".
			"DTSTART;TZID=Europe/Athens:".date("Ymd\THis",strtotime($start))."\r\n".
			"DTEND;TZID=Europe/Athens:".date("Ymd\THis",strtotime($end))."\r\n".
			"LOCATION:".$location."\r\n".
			"TRANSP: OPAQUE\r\n".
			"SEQUENCE:0\r\n".
			"UID:".date("Ymd\THis\Z").rand()."\r\n".
			"DTSTAMP:".date("Ymd\THis\Z")."\r\n".
			"SUMMARY:".$name."\r\n".
			"DESCRIPTION:".$description."\r\n".
			"PRIORITY:1\r\n".
			"CLASS:PUBLIC\r\n".
			"BEGIN:VALARM\r\n".
				"TRIGGER:-PT15M\r\n".
				"ACTION:DISPLAY\r\n".
				"DESCRIPTION:Reminder for the event\r\n".
			"END:VALARM\r\n".
		"END:VEVENT\r\n";
	}
	function send_mail($type){
		$to = $_SESSION['email'];
		
		$message = $this->data;
		
		if($type=="single"){
			$subject = "ICALC event for ".$this->name;
		}else{
			$subject = "ICALC events for all schedules";
			
		}
		$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion()."\r\n";
		$headers .= "Content-Type: text/calendar;\r\n";
		$headers .= "Content-class: urn:content-classes:calendarmessage\r\n";
		
		mail($to, $subject, $message, $headers);
	}
}

if((isset($_POST['ics_admin']) && $_POST['ics_admin']==1 ) ){
	$admin_ics = new ICS();
	$admin_ics->data ="BEGIN:VCALENDAR\r\nVERSION:2.0\r\n".
		"METHOD:REQUEST\r\n".
		"BEGIN:VTIMEZONE\r\n".
		"TZID:Europe/Athens\r\n".
		"PRODID:-//ISchedule//Outlook 15.0 MIMEDIR//EN\r\n".
		"END:VTIMEZONE\r\n";
	global $conn;
	
	if(isset($_POST['sch_id'])){
		$sch_id = mysql_escape_string(filter_var($_POST['sch_id'], FILTER_SANITIZE_NUMBER_INT));
		$q = "SELECT * FROM schedules WHERE id='".$sch_id."'";
		$type="single";
	}else{
		$q = "SELECT * FROM schedules";
		$type="multiple";
	}
	
	$result = mysql_query($q,$conn);
	while($row=mysql_fetch_array($result)){
		
		$day_id=$row['day']+1;
		$q_tab = "SELECT * FROM days WHERE id='".$day_id."'";
		$result_tab = mysql_query($q_tab,$conn);
		$row_tab=mysql_fetch_array($result_tab);
		
		if(strlen($row['start_minute'])==1){
			$start_time = "0".$row['start_minute'];
		}else{
			$start_time = $row['start_minute'];
		}
		
		if(strlen($row['fin_minute'])==1){
			$fin_time = "0".$row['fin_minute'];
		}else{
			$fin_time = $row['fin_minute'];
		}
		
		if(preg_match("/[0-9]{2}\/[0-9]{2}/", $row['title'], $matched)){
			$date_pieces = explode("/",$matched['0']);
			$date = $date_pieces['0']."-".$date_pieces['1']."-".date('Y');
		}elseif(preg_match("/(Δευτέρα|Τρίτη|Τετάρτη|Πέμπτη|Παρασκευή|Σάββατο|Κυριακή)/", $row['title'], $matched)){
			$i=-1;
			$found=0;
			$days = array('Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο');
			while($i<7 && $found==0){
				$i++;
				if($days[$i]==$matched[0]){
					$found=1;
				}
			}
			
			$days_left = $i - date('w');
			if($days_left < 0){
				$days_left = 6-$days_left;
			}
			
			$day = $days_left + date('d');
			$date = $day."-".date('m')."-".date('Y');

		}else{
			$date = $row['fin_date'];
		}
		
		$start=$date." ".$row['start_hour'].":".$start_time;
		$end=$date." ".$row['fin_hour'].":".$fin_time;
		$name=$row_tab['title']." ".$row['title'];
		$description = "Αυτοματοποιημένο Γεγονός από το ischedule";
		$location="40.301478, 21.800931";
		
		$admin_ics->ICS_update($start,$end,$name,$description,$location);
	}
	$admin_ics->data .= "END:VCALENDAR\r\n";
	$admin_ics->send_mail($type);
	
}

if((isset($_POST['ics_stud']) && $_POST['ics_stud']==1 ) ){
	$stud_ics = new ICS();
	$stud_ics->data ="BEGIN:VCALENDAR\r\nVERSION:2.0\r\n".
		"METHOD:REQUEST\r\n".
		"BEGIN:VTIMEZONE\r\n".
		"TZID:Europe/Athens\r\n".
		"PRODID:-//ISchedule//Outlook 15.0 MIMEDIR//EN\r\n".
		"END:VTIMEZONE\r\n";
	global $conn;
	
	$sch_info = mysql_escape_string(filter_var($_POST['sch_info'], FILTER_SANITIZE_STRING));
	$sch_info_array = explode("-", $sch_info);
	$q = "SELECT * FROM schedules WHERE id='".$sch_info_array['0']."'";
	$type="single";

	$result = mysql_query($q,$conn);
	$row=mysql_fetch_array($result);
	
	
	$day_id=$row['day']+1;
	$q_tab = "SELECT * FROM days WHERE id='".$day_id."'";
	$result_tab = mysql_query($q_tab,$conn);
	$row_tab=mysql_fetch_array($result_tab);
	
	$start_time = $sch_info_array['1'];
	
	$time_array = explode(":", $sch_info_array['1']);
	$start_minute = intval($time_array['1']);
	$fin_time = get_next_schedule_slot($start_minute, $row['minutes'], $time_array['0']);
	
	$date = get_schedule_date($row['title'], $row['fin_date']);
	
	$start=$date." ".$start_time;
	$end=$date." ".$fin_time;
	$name=$row_tab['title']." ".$row['title'];
	$description = "Δεσμευμένη Χρονοθυρίδα για το γεγονός ".$name." για το φοιτητή ".$_SESSION['fname']." ".$_SESSION['lname']." με ΑΕΜ ".$_SESSION['aem']." στο Εργαστήριο Ψηφιακών Συστημάτων και Αρχιτεκτονικής Υπολογιστών Διασταύρωση Καραμανλή και Λοϊζου στα Εργαστήρια Μηχανολόγων (http://arch.icte.uowm.gr/address.php) .";
	$location="40.301478, 21.800931";
	
	$stud_ics->ICS_update($start,$end,$name,$description,$location);
	$stud_ics->data .= "END:VCALENDAR\r\n";
	$stud_ics->send_mail($type);
	
}


function get_schedule_date($title,$fin_date){
	$date=0;
	
	if(preg_match("/[0-9]{2}\/[0-9]{2}/", $title, $matched)){
		$date_pieces = explode("/",$matched['0']);
		$date = $date_pieces['0']."-".$date_pieces['1']."-".date('Y');
	}elseif(preg_match("/(Δευτέρα|Τρίτη|Τετάρτη|Πέμπτη|Παρασκευή|Σάββατο|Κυριακή)/", $title, $matched)){
		$i=-1;
		$found=0;
		$days = array('Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο');
		while($i<7 && $found==0){
			$i++;
			if($days[$i]==$matched[0]){
				$found=1;
			}
		}
		
		$days_left = $i - date('w');
		if($days_left < 0){
			$days_left = 7+$days_left;
		}
		
		$date = date('d-m-Y', strtotime("+".$days_left." days"));

	}else{
		$date = $fin_date;
	}
	
	return $date;
}

function get_next_schedule_slot($start_minute, $duration, $start_hour){
	$mins= $start_minute + $duration;
	$carry=0;
	if($mins>=60){
		$carry = intval(floor($mins/60));
		$mins = $mins - $carry*60;
	}
	$hours= $start_hour + $carry;
	if($hours<10){
		$hours="0".$hours;
	}
	if($mins<10){
		$mins="0".$mins;
	}
	return $hours.":".$mins;
}


function verify_uowm_ip(){
	$ip=$_SERVER["REMOTE_ADDR"];
	$address=$ip;
	if (ip2long($ip)<ip2long("83.212.16.0") || ip2long($ip)>ip2long("83.212.25.255")){ 
		$uowm=FALSE;

	//BOUTSKIDIS:
	// K2
	//2001:648:2820:5200::/64
	//VPN
	//2001:648:2820:5301::/64

	//Lets check if this is on IPv6 range
	$first_in_range = "2001:648:2820:5200:0000:0000";
	//$last_in_range  = inet_pton('2001:648:2820:5301:ffff:ffff');
	//$last_in_range  = inet_pton('2001:648:2ffc:0121:ffff:ffff');
	//$last_in_range  = "2001:648:2ffc:121:ffff:ffff";
	$last_in_range  = "2001:648:2820:5301:0000:0000";


	if ($address >= $first_in_range && $address <= $last_in_range) 
		{
		 $uowm=TRUE; }
		else
		{ $uowm=FALSE; }


	}else{ 
		$uowm=TRUE; 
	}
	return $uowm;
}

?>
