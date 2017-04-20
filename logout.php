<?
session_start(); 
include("functions.php");

/**
 * Delete cookies - the time must be in the past,
 * so just negate what you added when creating the
 * cookie.
 */
 

if(( isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass']) ) || ( isset($_COOKIE['user']) || isset($_COOKIE['admin']) )){
	$past = time() - 60*60*24*100;
	foreach ( $_COOKIE as $key => $value )
	{
		setcookie( $key, $value, $past, '/' );
	}
	
}

?>

<html>
<head>
<title>Logging Out</title>
<link href="style.css" rel="stylesheet" type="text/css" media="screen"/>
</head>
<body>
<div id="main">
<div id="container"><center>
<?

if(!$logged_in){
   echo "<h1>Error!</h1>\n";
   echo "You are not currently logged in, logout failed. Back to <a href=\"index.php\">main</a>";
}
else{
   /* Kill session variables */

	session_destroy();   // destroy session.

   echo "<h1>Logged Out</h1>\n";
   echo "You have successfully <b>logged out</b>. Back to <a href=\"index.php\">main</a>";
}

?>
</center></div></div>
</body>
</html>