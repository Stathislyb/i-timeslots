<?
session_start();
include("functions.php");

function Printschedule($id){
	global $conn;
	echo "<head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
	echo '<link href="style.css" rel="stylesheet" type="text/css" media="screen"/>';
	echo '<link href="none.css"  rel="stylesheet" type="text/css" media="print"/></head><body class="none">';



	$query = "SELECT * FROM schedules WHERE id ='".$id."'";
	$res = mysql_query($query,$conn);
	$rows = mysql_fetch_array($res);
	if($rows['onoff'] == 1){
		//echo "<br /><br /><br />";
		//echo "<center> <h2>".$rows['title']."</h2> </center><br />";
		echo "<div><table id='datetable' border='1'><tr><td colspan='4' align='center'>".$rows['title']."</td></tr><tr><td>A/A</td> <td id='timetd'>Time</td> <td id='nametd'>Name</td> <td id='aemtd'>A.E.M.</td></tr>";
		$lock=0;
		$firsthourpassed=0;
		$de = 0;
		$min = "00";
		$tim = "00";
		$finalleftover=0;
		$stopschdl=0;
		$countrow=1;
		
			if($rows['start_minute'] < 10){
				$groupmin= "0".$rows['start_minute'];
			}else{
				$groupmin= $rows['start_minute'];
			}
			
			if($rows['seconds'] == 0){
				$groupsec= "0".$rows['seconds'];
			}else{
				$groupsec= $rows['seconds'];
			}
		
		$grouptime=$rows['start_hour'].":".$groupmin.":".$groupsec;
		
		if((($time['hours'] > $rows['start_hour']) || ($time['hours'] == $rows['start_hour'] && $time['minutes'] >= $rows['start_minute'])) && (($time['hours'] < $rows['fin_hour']) || ($time['hours'] == $rows['fin_hour'] && $time['minute'] <= $rows['fin_minute']))){
			$lock=1;
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
			
						if(isset($_SESSION['aem'])){
							if($rows['groupschedule']==1){
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$grouptime." </td> <td id='nametd'>" . $row['first_name'] . " " . $row['last_name'] . " </td><td id='aemtd'> " . $row['aem'] . " </td>" ;
							}else{
								echo "<tr><td>".$countrow.".</td><td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'>" . $row['first_name'] . " " . $row['last_name'] . " </td><td id='aemtd'> " . $row['aem'] . " </td></tr>" ;
							}
						}else {
							if($rows['groupschedule']==1){
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$grouptime." </td> <td id='nametd'>" . "HIDDEN" . " " . "(Log In to View)" . " </td><td id='aemtd'> " . $row['aem'] . " </td>" ;
							}else{
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'>" . "HIDDEN" . " " . "(Log In to View)" . " </td><td id='aemtd'> " . $row['aem'] . " </td></tr>" ;
							}
						}
						
					}else{ //we dont have a user slot here
						if(isset($_SESSION['aem'])){
							if($rows['groupschedule']==1){
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$grouptime." </td> <td id='nametd'> </td><td id='aemtd'> </td></tr>" ;
							}else{
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'> </td><td id='aemtd'> </td></tr>" ;
							}
						}else {

							if($rows['groupschedule']==1){
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$grouptime." </td> <td id='nametd'> </td><td id='aemtd'> </td></tr>" ;
							}else{
								echo "<tr><td>".$countrow.".</td> <td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'> </td><td id='aemtd'> </td></tr>" ;
							}

}





					}
					
					//echo "<tr> <td id='timetd'>".$j.":".$min.":".$tim." </td> <td id='nametd'>" . $row['first_name'] . " " . $row['last_name'] . " </td><td id='aemtd'> " . $row['aem'] . " </td></tr>" ;
					$countrow++;
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
	
		echo "</table><center><a href='index.php'>Back</a></center></body>";
	}
}

if(isset($_POST['printok']) || isset($_POST['printok_x']) ){
	$schedule_id=mysql_escape_string(filter_var($_POST['schedule_id'], FILTER_SANITIZE_NUMBER_INT));
	
	if($schedule_id == $_POST['schedule_id']){
		Printschedule( $schedule_id);;
	}
}


if(isset($_GET['schedule_id']) ){
        $schedule_id=mysql_escape_string(filter_var($_GET['schedule_id'], FILTER_SANITIZE_NUMBER_INT));
        
        if($schedule_id == $_GET['schedule_id']){
                Printschedule( $schedule_id);
        }
}


if(isset($_GET['all'])){
	
	$query = "SELECT id FROM schedules ORDER BY fin_date ASC";
	$res = mysql_query($query,$conn);

	while($row=mysql_fetch_assoc($res))
	{
		foreach($row as $key=>$value) { Printschedule( $value ); }
	}
}



?>
