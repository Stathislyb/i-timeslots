<?
session_start(); 
include("mobilefunctions.php"); 

/**
 * Delete cookies - the time must be in the past,
 * so just negate what you added when creating the
 * cookie.
 */
if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
	setcookie("cookname", "", time()-60*60*24*100, "/");
	setcookie("cookpass", "", time()-60*60*24*100, "/");
	setcookie("cookfname","", time()-60*60*24*100, "/");
	setcookie("cooklname","",time()-60*60*24*100, "/");
	setcookie("cookaem","",time()-60*60*24*100, "/");
	setcookie("cooktype","",time()-60*60*24*100, "/");
	setcookie("cookactive","",time()-60*60*24*100, "/");
	setcookie("cookid","",time()-60*60*24*100, "/");
}

?>

<!DOCTYPE html> 
<html> 
	<head> 
	<title>Online Manager</title> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.js"></script>
</head> 
<body"> 

<div data-role="page">

	<div data-role="header">
		<?
		if($logged_in == TRUE){
			echo "<h3>".$_SESSION['username']."</h3>";
		}else{
			echo "<h1>Online Manager</h1>";
		}
		?>
	</div><!-- /header -->

	<div data-role="content">	
		<div class="content-primary">
			<?

			if(!$logged_in){
			   echo "<h1>Error!</h1>\n";
			   echo "You are not currently logged in, logout failed. Back to <a href=\"index.php\">Home</a>";
			}
			else{
			   /* Kill session variables */
				unset($_SESSION['username']);
				unset($_SESSION['password']);
				unset($_SESSION['fname']);
				unset($_SESSION['lname']);
				unset($_SESSION['aem']);
				unset($_SESSION['type']);
				unset($_SESSION['active']);
				unset($_SESSION['id']);
				$_SESSION = array(); // reset session array
				session_destroy();   // destroy session.

			   echo "You have successfully <b>logged out</b>. Back to <a href=\"index.php\">main</a>";
			   echo "<script>document.location.href=\"index.php\";</script>";
			}

			?>
		</div>
	</div><!-- /content -->

	<div data-role="footer">
		<h4>&#169; Developed by Lymperidis Efstathios.</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>