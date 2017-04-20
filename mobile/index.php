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
	<link rel="stylesheet" href="jquery_mobile/jquery.mobile-1.1.1.min.css" />
	<script src="jquery_mobile/jquery-1.7.1.min.js"></script>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="jquery_mobile/jquery.mobile-1.1.1.min.js"></script>
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
		if($logged_in == TRUE){
			echo "<a href='logout.php' class='ui-btn-right'>Logout</a>";
		}else{
			echo "<a href='login.php' data-rel='dialog' class='ui-btn-right'>Login</a>";
		}
		?>
	</div><!-- /header -->

	<div data-role="content">	
		<div class="content-primary">
			<? showdays(); ?>
		</div>
	</div><!-- /content -->

	<div data-role="footer">
		<h4>&#169; Developed by Lymperidis Efstathios.</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>
