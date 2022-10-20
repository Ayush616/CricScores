<?php include 'db/connection.php';
//$mid=1;
//$q = mysqli_query($con, "SELECT * FROM playing11 WHERE (batStatus = 11 OR batStatus = 12) AND match_id='$mid'");
//            // $qstrike = mysqli_query($con, "SELECT * FROM playing11 WHERE batStatus = 11 AND match_id='$mid'");
//
//            $q = mysqli_fetch_assoc($q);
//
//            print_r($q);

function OversToBalls($overs){
    return floor($overs)*6 + ($overs*10)%10;
}

echo OversToBalls(5.5);

?>