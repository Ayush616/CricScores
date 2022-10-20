
<?php

include "db/connection.php";
include "functions.php";

$_SESSION['uid'] = 1;

//For Updating TEAM
if(isset($_POST['tname'])){
    if(!$hh=mysqli_query($con, "INSERT INTO team(tname, hostid, manager) VALUES('".$_POST['tname']."','".$_SESSION['uid']."','".$_POST['tmg']."')")){
        echo "Bhaag saale";
        echo mysqli_error($con);
    }
    
    
}

//For Updating players
if(isset($_POST['first'])){
    
    
    $output_dir = "uploads/players/";/* Path for file upload */
	$name      = str_replace(' ','-',strtolower($_FILES['file']['name']));
	$type      = $_FILES['file']['type'];
 
	$ext = substr($name, strrpos($name, '.'));
	$ext       = str_replace('.','',$ext);
	$name      = preg_replace("/\.[^.\s]{3,4}$/", "", $name);
	$name= $name.'-'.time().'.'.$ext;
    $ret[$name]= $output_dir.$name;
	
	/* Try to create the directory if it does not exist */
	if (!file_exists($output_dir))
	{
		@mkdir($output_dir, 0777);
	} 
    
    echo $_FILES["file"]["tmp_name"];
	if(move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir."/".$name )){
    
   $plquery = mysqli_query($con, "INSERT INTO players(firstname, lastname, type, tid, dob, image) VALUES('".$_POST['first']."','".$_POST['last']."','".$_POST['type']."','".$_POST['team']."','".$_POST['dob']."', '$name')");
        
        if($plquery) mysqli_query($con, "INSERT INTO playerstats(pid) VALUES('".mysqli_insert_id($con)."')");
 }else{
        echo 0;
    }
    
    
    //Make the team active
    $playercount = mysqli_num_rows(mysqli_query($con, "SELECT * FROM players WHERE tid = '".$_POST['team']."'"));
    echo $playercount;
    if($playercount>=15){
        if(!mysqli_query($con, "UPDATE team SET active=1 WHERE tid = '".$_POST['team']."'")){
            echo mysqli_error($con);
        }
    }
    
    
    
    
}

//Schedule a match

if(isset($_POST['steam1'])){
    mysqli_query($con, "INSERT INTO matches(tid1, tid2, datetime, place, hostid) VALUES('".$_POST['steam1']."', '".$_POST['steam2']."', '".$_POST['sdt']."', '".$_POST['dtplace']."', '".$_SESSION['uid']."')");
    echo 1;
}


// Update Toss

if(isset($_POST['toss'])){
    mysqli_query($con, "UPDATE matches SET toss='".$_POST['toss'].$_POST['electedTo']."' WHERE mid = '".$_POST['mid']."'");
    echo 1;
}

if(isset($_POST['updateInputs'])){
    $strikebat = mysqli_query($con, "SELECT * FROM playing11 WHERE match_id = ".$_POST['mid']." AND batStatus =11");
    echo mysqli_num_rows($strikebat);
    $strikebat = mysqli_fetch_assoc($strikebat);
    print_r($strikebat);
    $strikebatName = $strikebat['player_name'];
    $strikebatID = $strikebat['player_id'];

    $nstrikebat = mysqli_query($con, "SELECT * FROM playing11 WHERE match_id = ".$_POST['mid']." AND batStatus =12");
    $nstrikebat = mysqli_fetch_assoc($nstrikebat);
    $nstrikebatName = $nstrikebat['player_name'];
    $nstrikebatID = $nstrikebat['player_id'];

    $bowler = mysqli_query($con, "SELECT * FROM playing11 WHERE match_id = ".$_POST['mid']." AND bowlStatus =21");
    $bowler = mysqli_fetch_assoc($bowler);
    $bowlerName = $bowler['player_name'];
    $bowlerID = $bowler['player_id'];
    
    echo json_encode(array("strikeid"=>$strikebatID , "strikename"=>$strikebatName, "nstrikeid"=>$nstrikebatID, "nstrikename"=>$nstrikebatName, "bowlerID"=>$bowlerID, "bowlername"=>$bowlerName));
}


if(isset($_POST['tbowling'])){
    $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = ".$_POST['tbowling']." AND match_id = ".$_POST['mid']." AND bowlStatus NOT IN (20, 21)");

    echo '<option value="0">Select Bowler</option>';
    while($row=mysqli_fetch_assoc($q)){
        echo '<option value="'.$row['player_id'].'">'.$row['player_name'].'</option>';
    }
}

if(isset($_POST['tbatting'])){
    
    $matchInfo = getInfoByMatchId($_POST['mid'], $con);
    $firstInning = ($matchInfo['toss']==11 && $matchInfo['t1batting']==1) || ($matchInfo['toss']==21 && $matchInfo['t1batting']==0);

    $t1batting = getInfoByMatchId($_POST['mid'], $con)['t1batting'];

    if($t1batting==1){
        $wkts= getInfoByMatchId($_POST['mid'], $con)['t1wkts'] +1;
    }else{
        $wkts= getInfoByMatchId($_POST['mid'], $con)['t2wkts'] +1;
    }

    if($wkts == 10 && $firstInning==1){
        echo 'inningBreak';
    }else if($wkts == 10 && $firstInning==0){
        echo 'matchOver';
    }else{
        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = ".$_POST['tbatting']." AND match_id = ".$_POST['mid']." AND batStatus=0");
        echo '<option value="0">Select Batsman</option>';
            while($row=mysqli_fetch_assoc($q)){
                echo '<option value="'.$row['player_id'].'">'.$row['player_name'].'</option>';
            }
    }
   
}

if(isset($_POST['type']) && $_POST['type']=="updateBowler"){
    $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = ".$_POST['tbowling']." AND match_id = ".$_POST['mid']);

    $bowlers='';
    while($row=mysqli_fetch_assoc($q)){    
        $bowlers.='<option value="'.$row["player_id"].'">'.$row["player_name"].'</option>';
    }

    $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = ".$_POST['tbatting']." AND match_id = ".$_POST['mid']);

    $batsman='';
    while($row=mysqli_fetch_assoc($q)){    
        $batsman.='<option value="'.$row["player_id"].'">'.$row["player_name"].'</option>';
    }

    echo json_encode(array("bowler"=> $bowlers,"batsman"=> $batsman));
}

?>

