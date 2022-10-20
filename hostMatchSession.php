
<?php include 'functions.php';
date_default_timezone_set('Asia/Kolkata');
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
        <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="hostMatchSession.css">
    <link href="rome-master/dist/rome.css" rel="stylesheet">
    
    <link rel="stylesheet" href="jqueryUI/jquery-ui.css">
      <link rel="stylesheet" href="jqueryUI/jquery-ui.js">
</head>
<body>
    
    <?php
    if(isset($_GET['mid'])){
        
        $mid  = $_GET['mid'];
    ?>
    
    <h2>Scorer Room for Host</h2>
    
    <?php
        
        $matchinfo = getInfoByMatchId($mid, $con);
        
        
        $diff = getRemTime($matchinfo['datetime']);
        
        $tid1 = $matchinfo['tid1'];
        $tid2 = $matchinfo['tid2'];
        $team1 = getInfoByTeamId($tid1, $con);
        $team2 = getInfoByTeamId($tid2, $con);
        
        $team1 = $team1['tname'];
        $team2 = $team2['tname'];
        
        $curr_time = date("H:i:s");
        if($diff<=0){
        
            // Load Toss from Database
            $q= mysqli_query($con, "SELECT * FROM matches WHERE mid= '$mid'");
                $match = mysqli_fetch_assoc($q);
                $tossTeam = 0;
                $electedTo = 0;
                $TeamIDWonTheToss=0;
                $Opponent = 0;
                if($match['toss'] !=0){
                    $tossTeam = $match['toss'][0];
                    $electedTo = $match['toss'][1];
                }

                if($tossTeam==1){
                    $TeamIDWonTheToss = $match['tid1'];
                    $Opponent = $match['tid2']; 
                }
                else if($tossTeam==2){
                    $TeamIDWonTheToss = $match['tid2'];
                    $Opponent = $match['tid1'];
                }


            /////////
            
            
            
            

        ?>
       
       
    <div class="container Tossdiv text-light <?php if($tossTeam!=0) echo 'disable-div';?>" style="background: rgba(14,41,52, 0.9);">
      <input type="hidden" id="midval" value="<?php echo $_GET['mid']; ?>">
      <input type="hidden" id="matchSize" value="<?php echo 6*getInfoByMatchId($_GET['mid'], $con)['match_size']; ?>">
         <input type="hidden" id="team1" value="<?php echo $team1; ?>">
         <input type="hidden" id="team2" value="<?php echo $team2; ?>">

          <div id="toss">
           <h4 class="mb-4">Who won the toss?</h4>
            <span class="teambtn m-3 p-2 <?php if($tossTeam ==1){ echo 'wontoss';?>" style="background:rgb(204,229,255); color:black;" <?php }else echo '"'; ?> value="1">
                <?php echo $team1; ?>
            </span>

            <span class="teambtn m-3 p-2 <?php if($tossTeam ==2){ echo 'wontoss';?>" style="background:rgb(204,229,255); color:black;" <?php }else echo '"'; ?> value="2">
                <?php echo $team2; ?>
            </span>
          </div>
          
            <div id="electedtoss" class="mt-5">
                <h5>What they elected</h5>
            <span class="elecbtn m-3 p-2"><input type="checkbox" value="1" <?php if($electedTo ==1){;?>checked<?php }?>> Batting</span>

                <span class="elecbtn m-3 p-2"><input type="checkbox" value="0" <?php if($electedTo ==2){;?>checked<?php }?>> Bowling</span>
                <button class="btn btn-primary btn-sm" id="tossbtn">Proceed</button>
            </div>  
 
    </div>

 <!---Select Playing11 ---->
    <div class="container text-light" id="playersid" style="background: rgba(14,41,52, 0.9); margin-top: 20px; <?php if($match['toss']==0 || ($match['toss']!=0 && ($match['status']!=2))){?> display:none; <?php } ?>">
       <div class="row">
           <div class="col">
                <h5 class="mb-4">Select Playing 11 of <?php echo $team1; ?></h5>

                <div>
                   <ol class="selectablet1 selectstyle">
                    <?php
                        $q = mysqli_query($con, "SELECT * FROM players WHERE tid = '$tid1'");

                            while($row=mysqli_fetch_assoc($q)){
                    ?>
                    <li class="ui-widget-content" value="<?php echo $row['pid']; ?>"><?php echo $row['firstname'].' '.$row['lastname']; ?></li>

               <?php } ?>
               </ol>
                </div>
                
            </div>
            
            <div class="col">
                <h5 class="mb-4">Select Playing 11 of <?php echo $team2; ?></h5>

                <div>
                   <ol class="selectablet2 selectstyle">
                    <?php
                        $q = mysqli_query($con, "SELECT * FROM players WHERE tid = '$tid2'");
                            while($row=mysqli_fetch_assoc($q)){
                    ?>
                    <li class="ui-widget-content" value="<?php echo $row['pid']; ?>"><?php echo $row['firstname'].' '.$row['lastname']; ?></li>

               <?php } ?>
               </ol>
                </div>
            </div>
        </div>
        <button class="btn btn-primary btn-sm" style="right:0" id="selpro">Proceed</button>
    </div>
<!---------------------->

<?php
        $matchInfo = getInfoByMatchId($mid, $con);
    
        if($matchInfo['status']>=2){
            $firstInning = ($matchInfo['toss']==11 && $matchInfo['t1batting']==1) || ($matchInfo['toss']==21 && $matchInfo['t1batting']==0);

            $tidBat=$TeamIDWonTheToss;
            $tidBowl = 0;
            if(($electedTo == 1 && $firstInning) || ($electedTo == 2 && !$firstInning)){
                $tidBat = $TeamIDWonTheToss;
                $tidBowl = $Opponent;
            }elseif(($electedTo == 2 && $firstInning) || ($electedTo == 1 && !$firstInning)){
                $tidBowl = $TeamIDWonTheToss;
                $tidBat = $Opponent;
            }

        }

    ?>

<!--Start Inning -->
    <div class="container text-light" id="startInning" style="background: rgba(14,41,52, 0.9); margin-top: 20px; 
    <?php if($match['status']!=3 && $match['status']!=40){?> display:none; <?php } ?>">
    
         <div class="row">
             <div class="col">
                    
               <h5>Select Openers (<?php echo getInfoByTeamId($tidBat, $con)['tname'];?>)</h5>
                On Strike:
                <select name="" id="strikeOp">
                        <?php
                        // echo $tidBat;
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBat' AND match_id = '$mid'");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                </select>
            
                Non Strike:
                <select name="" id="nstrikeOp">
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBat' AND match_id = '$mid'");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                </select>
                
            </div>

            <div class="col">

                <h5>Select Bowler  (<?php echo getInfoByTeamId($tidBowl, $con)['tname'];?>)</h5>
                Bowler:
                <select name="" id="bowler">
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBowl' AND match_id = '$mid'");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                </select>
                
            </div>
        </div>

        <div class="row mt-3">
            <button class="btn btn-primary btn-sm" style="" id="stpro">Proceed</button>
        </div>
        
    </div>
    <!---------------------->

<?php
    //Load CurrentBatsmenandBowler

            $qs = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBat' AND match_id = '$mid' AND batStatus = 11");
            $qn = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBat' AND match_id = '$mid' AND batStatus = 12");
            
            $qs = mysqli_fetch_assoc($qs);
            $strike = $qs['player_id'];
            $strikename = $qs['player_name'];

            $qn = mysqli_fetch_assoc($qn);
            $nstrike = $qn['player_id'];
            $nstrikename = $qn['player_name'];
            
            $bowlq = mysqli_query($con, "SELECT * FROM playing11 WHERE bowlStatus = 21 AND match_id='$mid'");
            $bowler = mysqli_fetch_assoc($bowlq);
            $bowlerID = $bowler['player_id'];
            $bowlerName = $bowler['player_name'];
            ////////

?>

<input type="hidden" value="<?php echo $tidBowl?>" id="BowlingTeam">
<input type="hidden" value="<?php echo $tidBat?>" id="BattingTeam">
<input type="hidden" value="<?php echo $strike?>" id="strikeBat" data-pname="<?php echo $strikename?>">
<input type="hidden" value="<?php echo $nstrike?>" id="nstrikeBat" data-pname="<?php echo $nstrikename?>">
<input type="hidden" value="<?php echo $bowlerID?>" id="bowlerID" data-pname="<?php echo $bowlerName?>">
<input type="hidden" value="<?php if($firstInning==1){echo 1;}else{ echo 2;}?>" id="inning">

<?php echo $match['status']; ?>

    <!--- Play Match ---->
    <div class="container text-light" id="playMatch" style="background: rgba(14,41,52, 0.9); margin-top: 20px; 
    <?php if($match['status']!=4 && $match['status']!=41 && $match['status']!=42){?> display:none; <?php } ?>">

        <div class="summary">
                <div class="row justify-content-end">
                    <div class="col-3"><b>On Strike:</b> <span class="str">&nbsp;<?php echo $strikename?></span></div>
                    <div class="col-3"><b>On Non Strike:</b> <span class="nstr">&nbsp;<?php echo $nstrikename?></span></div>
                </div>
        </div>

        <hr style="border-top: 1px solid white;">
        <?php
            if(getInfoByMatchId($mid, $con)['t1batting']==1){
                $oversDone = $match['t1oversDone'];
            }else{
                $oversDone = $match['t2oversDone'];
            }

            ?>

           <input type="hidden" value="<?php echo OversToBalls($oversDone)+1;?>" id="bowlN">
            <div class="row my-3" id="bowlcat">
                <div class="col-4">Ball Category:</div>
                <div class="col legal"><span class="radio"><label><input type="radio" name="bcat" value="legal"> Legal</label></span></div>
                <div class="col noball"><span class="radio"><label><input type="radio" name="bcat" value="noball"> No Ball</label></span></div>
                <div class="col wide"><span class="radio"><label><input type="radio" name="bcat" value="wide"> Wide</label></span></div>
                <div class="col"></div>
                <div class="col"></div>
            </div>
            <div class="row my-3" id="out">
                <div class="col-4">Is Batsman Out:</div>
                <div class="col"><span class="radio"><label><input type="radio" name="batout" value="1"> Yes</label></span></div>
                <div class="col"><span class="radio"><label><input type="radio" name="batout" value="0"> No</label></span></div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
            </div>
            <!-- IF is Batsman Out == Yes -->
            <div class="row my-3" id="wkcat" style="display:none">
                <div class="col-4">Wicket Category:</div>
                <div class="col"><span class="radio"><label><input type="radio" class="bowled" name="wkcat" value="bowled"> Bowled</label></span></div>
                <div class="col"><span class="radio caught"><label><input type="radio" class="caught" name="wkcat" value="caught"> Caught</label></span></div>
                <div class="col"><span class="radio lbw"><label><input type="radio" class="lbw" name="wkcat" value="lbw"> LBW</label></span></div>
                <div class="col"><span class="radio htwkt"><label><input type="radio" class="htwkt" name="wkcat" value="htwkt"> HitWicket</label></span></div>
                <div class="col"><span class="radio ro"><label><input type="radio" class="ro" name="wkcat" value="ro"> Run Out</label></span></div>
            </div>
            <!-- IF Wicket Category == Caught -->
            <div class="row my-3" id="whoCaught" style="display:none">
                <div class="col-4">Who Caught:</div>
                <div class="col">
                    <select name="" id="caught">
                        <option value="0">Select Player</option>
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBowl' AND match_id = '$mid'");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['pid']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                    </select>

                </div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
            </div>
            <!-- IF Wicket Category == Run Out -->
            <div class="row my-3" id="runOut" style="display:none">
                <div class="col-4">Part played by Player or Players:</div>
                <div class="col">
                    <select name="" id="rop1">
                        <option value="0">Select Player 1</option>
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBowl' AND match_id = '$mid'");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                    </select>

                </div>
                <div class="col">
                    <select name="" id="rop2">
                        <option value="0">Select Player 2</option>
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBowl' AND match_id = '$mid'");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                    </select>

                </div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
                <div class="col"><span></span></div>
            </div>

            <div id="runOutPlayer" style="display:none">
                <div class="row my-3" >
                
                    <div class="col-4">Who Is Out?</div>
                    <div class="col p1"><span class="radio"><label><input type="radio" name="runOutPlayer" value=""></label></span></div>
                    <div class="col p2"><span class="radio"><label><input type="radio" name="runOutPlayer" value=""></label></span></div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col"></div>
                                
                </div>
            </div>

            
            
            <div id="runs" style="display:none">
                <p class="h5">Runs</p>
                <hr style="border-top: 1px solid white;">
                <div class="row my-3" >
                
                    <div class="col-4">Runs By Running b/w Wickets:</div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runs" value="1"> Single</label></span></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runs" value="2"> Double</label></span></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runs" value="3"> Triple</label></span></div>
                    <div class="col">OR--></div>
                    <div class="col"><input type="text" placeholder="Enter Runs"></div>
                                
                </div>
                <div class="row my-3">
                    <div class="col-4">Runs By Boundary:</div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runs" value="4"> Four</label></span></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runs" value="6"> Six</label></span></div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row my-3">
                    <div class="col-4"></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runs" value="0"> No Run</label></span></div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
            </div>
            <div id="runsThrough" style="display:none">
                <div class="row my-3" >
                
                    <div class="col-4">Runs through:</div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runsthrough" value="bat"> Bat</label></span></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runsthrough" value="bye"> Bye</label></span></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="runsthrough" value="lbye"> Leg Bye</label></span></div>
                    <div class="col"></div>
                    <div class="col"></div>
                                
                </div>
            </div>
        <div class="row mt-3">
            <button class="btn btn-primary btn-sm" style="" id="bowlpro">Proceed</button>
        </div>
        <div id="nextBatsman" style="<?php if($match['status']!=42){?> display:none; <?php } ?>">
                <div class="row my-3" >
                
                    <div class="col-4">Who Is the Next Batsman?</div>
                    <div class="col">
                    <select>
                        <option value="0">Select Player</option>
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBat' AND match_id = '$mid' AND batStatus=0");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                    </select>

                    </div>
                    <div class="col"></div>
                    <div class="col"><button class="btn btn-primary btn-sm" style="" id="nextBatBut">Proceed</button></div>
                    <div class="col"></div>
                    <div class="col"></div>
                                
                </div>
                <div id ="strikeChanged" class="row my-3" style="display:none;" >
                
                    <div class="col-4">Did the batsmen changed their strike?</div>
                    <div class="col"><span class="radio"><label><input type="radio" name="strikeChanged" value="1"> Yes</label></span></div>
                    <div class="col"><span class="radio"><label><input type="radio" name="strikeChanged" value="0"> No</label></span></div>
                    <div class="col"></div>
                    <div class="col"></div>
                    <div class="col"></div>
                                
                </div>
        </div>
        
        <div id="nextOver" style="<?php if($match['status']!=41){?> display:none; <?php } ?>">
                <div class="row my-3" >
                
                    <div class="col-4">Who Is the Next Bowler?</div>
                    <div class="col">
                    <select>
                        <option value="0">Select Bowler</option>
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM playing11 WHERE team_id = '$tidBowl' AND match_id = '$mid' AND bowlStatus NOT IN (20, 21)");
                        while($row=mysqli_fetch_assoc($q)){
                            ?>
                        <option value="<?php echo $row['player_id']; ?>"><?php echo $row['player_name'];?></option>

                        <?php }?>
                    </select>

                    </div>
                    <div class="col"></div>
                    <div class="col"><button class="btn btn-primary btn-sm" style="" id="nextOverBut">Proceed</button></div>
                    <div class="col"></div>
                    <div class="col"></div>
                                
                </div>
        </div>

    </div>

    
    <?php }else{
            echo "Match is yet to start";
        }
    } ?>
  <!------>  



</body>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="hostMatchSession.js"></script>
   
  <script src="rome-master/dist/rome.js"></script>
</html>