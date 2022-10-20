
<?php include 'functions.php';
date_default_timezone_set('Asia/Kolkata');


$plist1 = json_decode($_POST['plist1']);
$plist2 = json_decode($_POST['plist2']);

$plist = array();
 

    foreach($plist1 as $pid){
        $pname = getInfoByPlayerId($pid, $con)['firstname'].' '.getInfoByPlayerId($pid, $con)['lastname'];
        $tid = getInfoByPlayerId($pid, $con)['tid'];
        $tname = getInfoByTeamId($tid, $con)['tname'];
        $mid = $_POST['mid'];
        $q = mysqli_query($con, "INSERT INTO playing11 (player_id, team_id, match_id, player_name, team_name, iscaptain, iswk) VALUES ('$pid', '$tid', '$mid', '$pname', '$tname', 0, 0 )");
    }

    foreach($plist2 as $pid){
        $pname = getInfoByPlayerId($pid, $con)['firstname'].' '.getInfoByPlayerId($pid, $con)['lastname'];
        $tid = getInfoByPlayerId($pid, $con)['tid'];
        $tname = getInfoByTeamId($tid, $con)['tname'];
        $mid = $_POST['mid'];
        $q = mysqli_query($con, "INSERT INTO playing11 (player_id, team_id, match_id, player_name, team_name, iscaptain, iswk) VALUES ('$pid', '$tid', '$mid', '$pname', '$tname', 0, 0 )");
    }

    for($i=0; $i<sizeof($plist1); $i++){
        $plist1[$i] = getInfoByPlayerId($plist1[$i], $con)['firstname'].' '.getInfoByPlayerId($plist1[$i], $con)['lastname'];
    }

    for($i=0; $i<sizeof($plist2); $i++){
        $plist2[$i] = getInfoByPlayerId($plist2[$i], $con)['firstname'].' '.getInfoByPlayerId($plist2[$i], $con)['lastname'];
    }
    
    
    $plist['t1'] = $plist1;
    $plist['t2'] = $plist2;
echo json_encode($plist);

    ?>

