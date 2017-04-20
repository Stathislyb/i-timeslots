<? 
/* Include Files *********************/
session_start(); 
include("mobilefunctions.php"); 
/*************************************/
?>

<!DOCTYPE html> 
<html> 
	<head> 
	<title>Online Manager</title> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.js"></script>
	<script>$('#successlogin').popup('open');</script>
</head> 
<body> 

<div data-role="page">

	<div data-role="header">
		<h1>Online Manager</h1>
		<a href="index.php" class="ui-btn-right">Cancel</a>
	</div><!-- /header -->

	<div data-role="content" data-inset="true">	
		<div class="content-primary">
			<form action="index.php" method="POST">
				<fieldset>

					<label for="username">Username:</label>
					<input type="text" name="user" id="user" value=""  />

					<label for="password">Password:</label>
					<input type="password" name="pass" id="pass" value="" />

				   <input id="sublogin_mob" name="sublogin_mob" type="submit" value="Login" data-role="button" data-inline="true" data-theme="b" />

			   </fieldset>
			</form>
		</div>
	</div>
	

	<div data-role="footer">
		<h4>&#169; Developed by Lymperidis Efstathios.</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>