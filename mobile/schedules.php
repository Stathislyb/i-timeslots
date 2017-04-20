<? 
/* Include Files *********************/
session_start(); 
include("mobilefunctions.php"); 
/*************************************/
?>

<!DOCTYPE html> 
<html> 
	<head> 
	<title>Online Mobile Manager</title> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.js"></script>
</head> 
<body"> 

<div data-role="page">

	<div data-role="header">
		<a href="index.php" class="ui-btn-left">Home</a>
		
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
			<? 
				if(isset($_GET['id'])){
					$id=mysql_escape_string(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));

					if(($_GET['id']==$id) ){
						echo "<ul data-role='listview' class='ui-listview'><li data-role=list-divider role=heading class=ui-li ui-li-divider ui-bar-b>Schedule</li> ";
						showschedule($id);
						echo "</ul>";
					}else{
						echo "Invalide schedule.";
					}
				}else{
					echo "No schedule selected.";
				}

			?>
		</div>
	</div><!-- /content -->

	<div data-role="footer">
		<h4>&#169; Developed by Lymperidis Efstathios. Supervised by <a href="http://arch.icte.uowm.gr/mdasyg">Minas Dasygenis</a> </h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>
