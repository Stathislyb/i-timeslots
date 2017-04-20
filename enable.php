<?
include("database.php");

function endisable($id){
   	global $conn;
	$q = "SELECT * FROM schedules where id='".$id."'";
	$result = mysql_query($q,$conn);
	$row = mysql_fetch_array($result);
	//var_dump($row);
	
	if($row['onoff'] == 1){
		$query = "UPDATE schedules SET onoff='0' WHERE id='".$id."'";
		//echo "Disabled";
		return mysql_query($query,$conn); 
	}else {
		$query = "UPDATE schedules SET onoff='1' WHERE id='".$id."'";
		//echo "Enabled";
		return mysql_query($query,$conn); 
	}
}

if(isset($_REQUEST['id'])){
	$sch_id=mysql_escape_string(filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT));

	endisable($sch_id);
	
}



?>
