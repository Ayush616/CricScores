<?php

include "db/connection.php";


function getInfoByTeamId($tid, $con){
    $q = mysqli_query($con, "SELECT * FROM team WHERE tid = '$tid'");
    
    return mysqli_fetch_array($q);
}

function getInfoByPlayerId($pid, $con){
    $q = mysqli_query($con, "SELECT * FROM players WHERE pid = '$pid'");
    
    return mysqli_fetch_array($q);
}

function getInfoByMatchId($mid, $con){
    $q = mysqli_query($con, "SELECT * FROM matches WHERE mid = '$mid'");
    return mysqli_fetch_array($q);
}

function getTimeString($matchstart){
    $curr_time = (int)strtotime(date("H:i:s"));
    $match_start_time = (int)strtotime($matchstart);
    $rem_time = $match_start_time - $curr_time;
                
                $days = floor($rem_time / (60 * 60 * 24));
                  $hours = floor(($rem_time % (60 * 60 * 24)) / (60 * 60));
                  $minutes = floor(($rem_time % (60 * 60)) / (60));
                  $seconds = floor(($rem_time % (60)));
                  $value = $days."d ".$hours."h ".$minutes."m ".$seconds."s ";
    
    echo $value;
}

function getRemTime($matchstart){
    $curr_time = (int)strtotime(date("H:i:s"));
    $match_start_time = (int)strtotime($matchstart);
    $rem_time = $match_start_time - $curr_time;
                
    return $rem_time;
}

function getInfoByPlaying11Id($pid, $con, $mid){
    $q = mysqli_query($con, "SELECT * FROM playing11 WHERE player_id = '$pid' AND match_id = '$mid'");
    
    return mysqli_fetch_array($q);
}

//function getTeamsByMatchID($mid){
//    $a = array();
//    $q = mysqli_query($con, "SELECT * FROM matches WHERE mid = '$mid'");
//    array_push($a, )
//    
//    return mysqli_fetch_array($q);
//}

function getPlayersNameList($con, $plist){
    for($i=0; $i<sizeof($plist); $i++){
        $plist[$i] = getInfoByPlayerId($plist[$i], $con)['firstname'].' '.getInfoByPlayerId($plist[$i], $con)['lastname'];
    }
    return $plist;
}


function InsertPlaying11ToDB($con, $plist, $mid){
    foreach($plist as $pid){
        $pname = getInfoByPlayerId($pid, $con)['firstname'].' '.getInfoByPlayerId($pid, $con)['lastname'];
        $tid = getInfoByPlayerId($pid, $con)['tid'];
        $tname = getInfoByTeamId($tid, $con)['tname'];
        $pcheck = mysqli_query($con, "SELECT * FROM playing11 WHERE player_id = '$pid' and match_id = '$mid'");     
        if(mysqli_num_rows($pcheck)==0){
            $q = mysqli_query($con, "INSERT INTO playing11 (player_id, team_id, match_id, player_name, team_name, iscaptain, iswk) VALUES ('$pid', '$tid', '$mid', '$pname', '$tname', 0, 0 )");
        }
        
    }
}

function InsertCommentary($con, $mid, $comments){

$q = mysqli_query($con, "INSERT INTO Commentaries (mid, Commentaries) VALUES ('$mid', '$comments')");
        
}

function updateMatchStatus($con, $mid, $status){
    $q = mysqli_query($con, "UPDATE `matches` SET status = '$status' WHERE mid = '$mid'");
}

function updatePlayerStatus($con, $pid, $status, $mid, $ptype){
    if($ptype=='bat'){
        $q = mysqli_query($con, "UPDATE `playing11` SET batStatus = '$status' WHERE player_id = '$pid' AND match_id= '$mid'");
    }else{
        $q = mysqli_query($con, "UPDATE `playing11` SET bowlStatus = '$status' WHERE player_id = '$pid' AND match_id= '$mid'");
    }
    
}

function updateMatchCol($con, $col, $value, $mid){
    $q = mysqli_query($con, "UPDATE `matches` SET ".$col." = '$value' WHERE mid = '$mid'");
}

function updatePlayerCol($con, $col, $pid, $value, $mid){
    $q = mysqli_query($con, "UPDATE `playing11` SET ".$col." = '$value' WHERE player_id = '$pid' AND match_id= '$mid'");
}

function incrementBall($ball){
    $ball = $ball+0.1;

    if($ball > 0.6){
        floor($ball)+($ball%0.6);
    }
}


function ballsToOvers($balls){
    if($balls%6==0){
        return (($balls/6)-1)+0.6;
    }
    return floor($balls/6)+(($balls%6)/10);
}
function OversToBalls($overs){
    return floor($overs)*6 + ($overs*10)%10;
}

?>