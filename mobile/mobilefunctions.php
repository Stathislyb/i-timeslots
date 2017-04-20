<?

include("../database.php");







/*  Creates list dividers for days  */



function showdays(){
	global $conn;
	$i=0;

	$query = "SELECT * FROM days";
	$res = mysql_query($query,$conn);
	while($row = mysql_fetch_array($res)){

	

		if($row['onoff']==1){
			echo "<ul data-role='listview' class='ui-listview'><li data-role=list-divider role=heading class=ui-li ui-li-divider ui-bar-b>".$row['title']."</li> ";
			Showschedulesoftheday($i); 
			echo "</ul>";

		}
	$i++;
	}
}







/*  Creates list for schedules of each day  */



function Showschedulesoftheday($day){
	global $conn;
	$k=0;

	$query = "SELECT * FROM schedules WHERE day ='".$day."'";
	$res = mysql_query($query,$conn);
	while ($rows = mysql_fetch_array($res)){
		if($rows['onoff'] == 1){
			$k++;
			echo "<li data-corners='false' data-shadow='false' data-iconshadow='true' data-wrapperels='div' data-icon='arrow-r' data-iconpos='right' data-theme='c' class='ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c'><div class='ui-btn-inner ui-li'><div class='ui-btn-text'><a href='schedules.php?id=".$rows['id']."' class='ui-link-inherit'>".$rows['title']."</a></div><span class='ui-icon ui-icon-arrow-r ui-icon-shadow'>&nbsp;</span></div></li>";
		}
	}

	if($k==0){
		echo "<li data-corners='false' data-shadow='false' data-iconshadow='true' data-wrapperels='div' data-theme='c' class='ui-li ui-li-static ui-btn-up-c'><div class='ui-btn-inner ui-li'><div class='ui-btn-text'>No schedules.</div></div></li>";
	}

}







/*  Creates list of schedule's slots  */



function showschedule($id){
	global $conn;
	$time=getdate();
	
	$query = "SELECT * FROM schedules WHERE id='".$id."' ORDER BY fin_date ASC";
	$res = mysql_query($query,$conn);
	while ($rows = mysql_fetch_array($res)){

	

		if($rows['onoff'] == 1){

			$lock=0;
			$firsthourpassed=0;
			$de = 0;
			$min = "00";
			$tim = "00";
			$stopschdl=0;
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

							echo "<li data-corners='false' data-shadow='false' data-iconshadow='true' data-wrapperels='div' data-theme='c' class='ui-li ui-li-static ui-btn-up-c'><div class='ui-btn-inner ui-li'><div class='ui-btn-text'>";
							if($rows['groupschedule']==1){
								echo "<p class='ui-li-aside ui-li-desc'><strong>".$grouptime."</strong></p> <h3 class='ui-li-heading'>Name : " . $row['first_name'] . " " . $row['last_name'] . "</h3> <p class='ui-li-desc'><strong>AEM : " . $row['aem'] . " </strong></p>" ;
							}else{
								echo "<p class='ui-li-aside ui-li-desc'><strong>".$j.":".$min.":".$tim."</strong></p> <h3 class='ui-li-heading'>Name : " . $row['first_name'] . " " . $row['last_name'] . "</h3> <p class='ui-li-desc'><strong>AEM : " . $row['aem'] . " </strong></p>" ;
							}
							echo "</div></div></li>";

						}else{

							echo "<li data-corners='false' data-shadow='false' data-iconshadow='true' data-wrapperels='div' data-icon='arrow-r' data-iconpos='right' data-theme='c' class='ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c'><div class='ui-btn-inner ui-li'><div class='ui-btn-text'>";
							
							if(isset($_SESSION['username'])){
								echo "<a href='mobilefunctions.php?id=".$rows['id']."&date_id=".$date_id."&user_id=".$_SESSION['id']."&gid=".$rows['gid']."' class='ui-link-inherit'>";
							}else{
								echo "<a href='login.php' data-rel='dialog' class='ui-link-inherit'>";
							}
							if($rows['groupschedule']==1){
								echo "<tr> <td id='timetd'>".$grouptime." </td> <td id='nametd'> </td><td id='aemtd'> </td>" ;
							}else{
								echo "<tr> <td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'> </td><td id='aemtd'> </td>" ;
							}
							echo "</a></div><span class='ui-icon ui-icon-arrow-r ui-icon-shadow'>&nbsp;</span></div></li>";

						}

						echo "</li>";
						
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
		}
	}
}







/* Register user into selected schedule slot. */



function RegisterNewDate($date_id, $user_id, $gid){

	global $conn;

	$q = "DELETE FROM studentslots WHERE user_id='".$user_id."' AND gid='".$gid."' ";

	mysql_query($q,$conn);

	

	$query = "INSERT INTO studentslots (user_id, sch_date_id, gid) VALUES ($user_id, $date_id, $gid) ";

	return  mysql_query($query,$conn);



}



if(isset($_GET['date_id'])){

	$date_id=mysql_escape_string(filter_var($_GET['date_id'], FILTER_SANITIZE_NUMBER_INT));

	$user_id=mysql_escape_string(filter_var($_GET['user_id'], FILTER_SANITIZE_NUMBER_INT));

	$gid=mysql_escape_string(filter_var($_GET['gid'], FILTER_SANITIZE_NUMBER_INT));

	if(($date_id == $_GET['date_id']) && ($user_id == $_GET['user_id']) && ($gid == $_GET['gid'])){

		RegisterNewDate($_GET['date_id'], $_GET['user_id'], $_GET['gid']);

	}

	header( 'Location: schedules.php?id='.$_GET['id'] ) ;

}







/* LOG IN Functions */









/**

 * Checks to see if the user has submitted his

 * username and password through the login form,

 * if so, checks authenticity in database and

 * creates session.

 */

if(isset($_POST['sublogin_mob'])){

	$user = mysql_escape_string(filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING));

	$pass = mysql_escape_string(filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING));

	

	if(($_POST['user'] == $user) && ($_POST['pass'] == $pass)){

		$_POST['user'] = $user;

		$_POST['pass'] = $pass;

   }else {

		die("You have enter invalid username and/or password.");

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

   $_POST['user'] = stripslashes($_POST['user']);

   $_SESSION['username'] = $_POST['user'];

   $_SESSION['password'] = $md5pass;

   $_SESSION['id'] = getuid($_POST['user']);

   $_SESSION['fname'] = getfname($_POST['user']);

   $_SESSION['lname'] = getlname($_POST['user']);

   $_SESSION['aem'] = getaem($_POST['user']);

   $_SESSION['type'] = getype($_POST['user']);

   $_SESSION['active'] = getact($_POST['user']);

   



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

		setcookie("cookfname",$_SESSION['fname'], time()+60*60*24*100, "/");

		setcookie("cooklname",$_SESSION['lname'], time()+60*60*24*100, "/");

		setcookie("cookaem",$_SESSION['aem'], time()+60*60*24*100, "/");

		setcookie("cooktype",$_SESSION['type'], time()+60*60*24*100, "/");

		setcookie("cookactive",$_SESSION['active'], time()+60*60*24*100, "/");

		setcookie("cookid",$_SESSION['id'], time()+60*60*24*100, "/");

   }



   /* Quick self-redirect to avoid resending data on refresh */

	echo "<meta http-equiv=\"Refresh\" content=\"0;url=$HTTP_SERVER_VARS[PHP_SELF]\">";

	//echo "<meta http-equiv='REFRESH' content='0;url=index.php'>";

	//echo "<script>document.location.href=\"index.php\";</script>";

	//header( 'Location: index.php' ) ;



   //return;

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

  

  

/**

 * checkLogin - Checks if the user has already previously

 * logged in, and a session with the user has already been

 * established. Also checks to see if user has been remembered.

 * If so, the database is queried to make sure of the user's 

 * authenticity. Returns true if the user has logged in.

 */

 

function checkLogin(){

   /* Check if user has been remembered */

   if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){

		

		

		$_SESSION['username'] = mysql_escape_string(filter_var(trim($_COOKIE['cookname']), FILTER_SANITIZE_STRING));

		$_SESSION['password'] = mysql_escape_string(filter_var(trim($_COOKIE['cookpass']), FILTER_SANITIZE_STRING));

		$_SESSION['fname'] = mysql_escape_string(filter_var(trim($_COOKIE['cookfname']), FILTER_SANITIZE_STRING));

		$_SESSION['lname'] = mysql_escape_string(filter_var(trim($_COOKIE['cooklname']), FILTER_SANITIZE_STRING));

		$_SESSION['aem'] = mysql_escape_string(filter_var($_COOKIE['cookaem'], FILTER_SANITIZE_NUMBER_INT));

		$_SESSION['type'] = mysql_escape_string(filter_var($_COOKIE['cooktype'], FILTER_SANITIZE_NUMBER_INT));

		$_SESSION['active'] = mysql_escape_string(filter_var($_COOKIE['cookactive'], FILTER_SANITIZE_NUMBER_INT));

		$_SESSION['id'] = mysql_escape_string(filter_var($_COOKIE['cookid'], FILTER_SANITIZE_NUMBER_INT));

   }



   /* Username and password have been set */

   if(isset($_SESSION['username']) && isset($_SESSION['password'])){

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

         return false;

      }

      return true;

   }

   /* User not logged in */

   else{

      return false;

   }

}

 

 



/* Sets the value of the logged_in variable, which can be used in your code */

$logged_in = checkLogin();



?>