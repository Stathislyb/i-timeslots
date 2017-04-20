<?php
$host="127.0.0.1"; // Host name 
$username=""; // Mysql username 
$password=""; // Mysql password 
$db_name="wwgorg_uowm"; // Database name 
$tbl_name="users"; // Table name

 //error_reporting(E_ALL); 
  //ini_set("display_errors", 1);

// Connect to server and select databse.
try { 
$conn = @mysql_connect("$host", "$username", "$password") or die("cannot connect to database. Maybe the administrator is performing a system upgrade. Dont worry, the periodic check script will correct this error soon. <br />In the meen time, why don't you check the static webpages of this site at <a href=http://arch.icte.uowm.gr>http://arch.icte.uowm.gr</a>?"); 
mysql_select_db("$db_name") or die("cannot select DB");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8");
} catch (Exception $e) {
    //echo 'Database Caught exception: ',  $e->getMessage(), "\n";
    header("Location: /index.php");
    die();
}


function connect(){


$host="127.0.0.1"; // Host name
$user=""; // Mysql username
$passwd=""; // Mysql password
$dbname="wwgorg_uowm"; // Database name


try{
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$passwd);
$conn->exec("SET NAMES 'utf8'");
}
catch(PDOException $pe){
die('Connection error:' . $pe->getmessage());
}
return $conn;
}

$database_already_here=1;
?>
