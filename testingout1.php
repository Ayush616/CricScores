<?php include "testingout.php";

   // try to conncet to database
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "olcademy1";
// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {  
    header("Location: site_maintenance.php");
}


function array_search_partial($arr, $keyword) {
  foreach($arr as $index => $string) {
      if (preg_match('/\b'.$keyword.'\b/', $string))
      return $index;
  }
    return -1;
}
/************** Course Search By Trainer Name *****************/

$trainer = array();

$qc = mysqli_query($con,"SELECT * from Courses");
$qw = mysqli_query($con,"SELECT * from Webinars");

 while($row= mysqli_fetch_assoc($qc)){
  array_push($trainer, strtoupper($row['trainer_name']));
 }
 while($row= mysqli_fetch_assoc($qw)){
  array_push($trainer, strtoupper($row['trainer_name']));
 }

 $trainer = array_unique($trainer);
 $trainer = array_filter($trainer);


$search = '2342ddd';
echo $i = array_search_partial($trainer, strtoupper($search));
if($i) $trainer[$i];

?>