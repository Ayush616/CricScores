<?php 
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class Chat implements MessageComponentInterface {
    protected $clients;
    public $con;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "Server Started";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        include_once "../functions.php";
        $con = mysqli_connect('localhost', 'root', '', 'cricscores');  // Connection Established with database;



        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        
        $comm = json_decode($msg, 'true');
        // print_r($comm);
        if($comm['step']=='toss'){
            $msg1 = "<p><b>".$comm['teamToss']."</b> won the toss and have elected to <b>".$comm['elected']."</b> first</b></p>";
            $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            InsertCommentary($con, $comm['mid'], $msg1);
            updateMatchStatus($con, $comm['mid'], 2);


            $matchInfo = getInfoByMatchId($comm['mid'], $con);

            if($matchInfo['toss']==11){
                updateMatchCol($con, 't1batting', 1, $comm['mid']);
            }else{
                updateMatchCol($con, 't1batting', 0, $comm['mid']);
            }
            
        }
        else if($comm['step']=='team'){
            $team1 = $comm['t1'];
            $team2 = $comm['t2'];
            $tid1 = getInfoByPlayerId($team1[0], $con)['tid'];
            $tid2 = getInfoByPlayerId($team2[0], $con)['tid'];

            $t1name = getInfoByTeamId($tid1, $con)['tname'];
            $t2name = getInfoByTeamId($tid2, $con)['tname'];

            $t1list = getPlayersNameList($con, $team1);
            $t2list = getPlayersNameList($con, $team2);

            $mid = $comm['mid'];

            print_r($t1list);
            $msg1 = "<p><b>".$t1name." (Playing 11): </b>".implode(', ',$t1list)."</p><p><b>".$t2name." (Playing 11): </b>".implode(', ',$t2list)."</p>";
            $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            InsertPlaying11ToDB($con, $team1, $mid);
            InsertPlaying11ToDB($con, $team2, $mid);

            updateMatchStatus($con, $comm['mid'], 3);

            InsertCommentary($con, $mid, $msg1);
        }

        else if($comm['step']=='startSession'){
            $strike = getInfoByPlayerId($comm['strike'], $con)['firstname'].' '.getInfoByPlayerId($comm['strike'], $con)['lastname'];
            $strikefname = getInfoByPlayerId($comm['strike'], $con)['firstname'];
            $nstrike = getInfoByPlayerId($comm['nstrike'], $con)['firstname'].' '.getInfoByPlayerId($comm['nstrike'], $con)['lastname'];
            $bowler = getInfoByPlayerId($comm['bowler'], $con)['firstname'].' '.getInfoByPlayerId($comm['bowler'], $con)['lastname'];
            $mid = $comm['mid'];

            $msg1 = "<p><b>".$strike." and ".$nstrike." are at the crease. ".$strikefname." is on Strike. ".$bowler." will open the attack.</b></p>";
            $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            updateMatchStatus($con, $mid, 4);
            updatePlayerStatus($con, $comm['strike'], '11', $mid, 'bat');
            updatePlayerStatus($con, $comm['nstrike'], '12', $mid, 'bat');
            updatePlayerStatus($con, $comm['bowler'], '21', $mid, 'bowl');

            InsertCommentary($con, $mid, $msg1);
        }

        else if($comm['step']=='bowls'){
            $strike = getInfoByPlayerId($comm['strike'], $con)['firstname'].' '.getInfoByPlayerId($comm['strike'], $con)['lastname'];
            $strikefname = getInfoByPlayerId($comm['strike'], $con)['firstname'];
            $nstrike = getInfoByPlayerId($comm['nstrike'], $con)['firstname'].' '.getInfoByPlayerId($comm['nstrike'], $con)['lastname'];
            $bowler = getInfoByPlayerId($comm['bowler'], $con)['firstname'];
            $bowl = $comm['bowl'];
            $mid = $comm['mid'];

            $matchInfo = getInfoByMatchId($mid, $con);

            $islegal = $comm['legal'];
            $bowlCat = $comm['bowlCat'];
            $isOut = $comm['isOut'];
            $wcat = $comm['wcat'];
            $run = $comm['run'];
            $runsThrough = $comm['runsThrough'];
            $teamBatting = ($matchInfo['t1batting']==1) ? $matchInfo['tid1'] : $matchInfo['tid2'];

            
            if($comm['runoutPlayer'] == 0){
                $runOutPlayer =0;
            }else{
                $runOutPlayer = getInfoByPlayerId($comm['runoutPlayer'], $con)['firstname'].' '.getInfoByPlayerId($comm['runoutPlayer'], $con)['lastname'];
            }

            if($comm['runoutP1'] == 0){
                $rop1 =0;
            }else{
                $rop1 = getInfoByPlayerId($comm['runoutP1'], $con)['firstname'];
            }
            if($comm['runoutP2'] == 0){
                $rop2 =0;
            }else{
                $rop2 = getInfoByPlayerId($comm['runoutP2'], $con)['firstname'];
            }
            
           

            if($bowlCat == 'wide'){
                $bowlCat ='<b>wide</b>';
            }elseif($bowlCat == 'bye'){
                $bowlCat ='<b>Byes</b>';
            }elseif($bowlCat == 'lbye'){
                $bowlCat ='<b>Leg Bye</b>';
            }else{
                $bowlCat = '<b>no ball</b>';
            }

            if($run=='1'){
                $run = $run.' run';
            }else if($run=='2' || $run=='3'){
                $run = $run.' runs';
            }else if($run == '4'){
                $run = '<b>FOUR</b>';
            }else if($run == '6'){
                $run = '<b>SIX</b>';
            }else{
                $run = 'no run';
            }

            

            $playerRuns = getInfoByPlaying11Id($comm['strike'], $con, $mid)['runsScored'];
            $playerBowlsPlayed = getInfoByPlaying11Id($comm['strike'], $con, $mid)['bowlsPlayed'];
            $player4sCount = getInfoByPlaying11Id($comm['strike'], $con, $mid)['4s'];
            $player6sCount = getInfoByPlaying11Id($comm['strike'], $con, $mid)['6s'];
            $playerBowlsBowled = getInfoByPlaying11Id($comm['bowler'], $con, $mid)['oversBowled'];
            $runsGiven = getInfoByPlaying11Id($comm['bowler'], $con, $mid)['runsGiven'];
            $tid1extras = $matchInfo['t1extrasGiven'];
            $tid2extras = $matchInfo['t2extrasGiven'];
            $t1runs = $matchInfo['t1runs'];
            $t1wkts = $matchInfo['t1wkts'];
            $t2runs = $matchInfo['t2runs'];
            $t2wkts = $matchInfo['t2wkts'];
            $bowlerExtras = getInfoByPlaying11Id($comm['bowler'], $con, $mid)['extrasGiven'];
            $wicketsTaken = getInfoByPlaying11Id($comm['bowler'], $con, $mid)['wicketsTaken'];

            $over = ballsToOvers($bowl); 

            if($isOut==1 && $islegal==1){  //Out in Legal Delivery.


                //Pending -- Runs taken while runOut is pending to be implemented.

                if($wcat == 'ro'){ //runout
                    $wcat = 'run out';

                    if($rop2==0){
                        $runout = $rop1;;
                    }else{
                        $runout = $rop1 .'/'. $rop2;
                    }
                    $msg1 = '<p><b>'.$over.'</b>  '.$bowler.' to '.$strike.', <b>OUT!</b> '.$wcat.'. <b>'.$runOutPlayer.' ('.$runout.')</b></p>';
                    // updatePlayerCol($con, 'batStatus', $comm['runoutPlayer'], '10', $mid);
                    $msg = json_encode(array("commentary"=> $msg1, "type"=> 'comm'));
                    $runsGiven = $runsGiven+$comm['run'];
                    updatePlayerCol($con, 'runsGiven', $comm['bowler'], $runsGiven, $mid);
                
                }else{
                    $msg1 = '<p><b>'.$over.'</b>  '.$bowler.' to '.$strike.', <b>OUT!</b> '.$wcat.'. </p>';
                    $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
                    $wicketsTaken++;
                    updatePlayerCol($con, 'wicketsTaken', $comm['bowler'], $wicketsTaken, $mid);

                    // updatePlayerCol($con, 'batStatus', $comm['strike'], '10', $mid); // Updating wicket of batsman
                }
                
                //Updating bowls played..
                $playerBowlsPlayed++;
                updatePlayerCol($con, 'bowlsPlayed', $comm['strike'], $playerBowlsPlayed, $mid); 

                //Incrementing bowls bowled for bowlers
                $playerBowlsBowled = OversToBalls($playerBowlsBowled)+1;
                $playerBowlsBowled = ballsToOvers($playerBowlsBowled);
    
                updatePlayerCol($con, 'oversBowled', $comm['bowler'], $playerBowlsBowled, $mid);

                //Updating batting team score.
                if($matchInfo['t1batting']==1){
                    $t1runs=$t1runs+$comm['run'];
                    updateMatchCol($con, 't1runs', $t1runs, $mid);

                    $t1wkts++;
                    updateMatchCol($con, 't1wkts', $t1wkts, $mid);
                }else{
                    $t2runs=$t2runs+$comm['run'];
                    updateMatchCol($con, 't2runs', $t2runs, $mid);

                    $t2wkts++;
                    updateMatchCol($con, 't2wkts', $t2wkts, $mid);
                }

            }else if($isOut==1 && $islegal==0){ //Out in Illegal Delivery.

                $wcat = 'run out';
                if($rop2==0){
                    $runout = $rop1;;
                }else{
                    $runout = $rop1 .'/'. $rop2;
                }
                $msg1 = '<p><b>'.$over.'</b>  '.$bowler.' to '.$strike.', <b>OUT!</b> '.$wcat.'. <b>'.$runOutPlayer.' ('.$runout.')</b></p>';
                $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
                // Updating wicket of batsman
                // updatePlayerCol($con, 'batStatus', $comm['runoutPlayer'], '10', $mid);

                //Updating bowls played..
                $playerBowlsPlayed++;
                updatePlayerCol($con, 'bowlsPlayed', $comm['strike'], $playerBowlsPlayed, $mid); 

                $runsGiven = $runsGiven+$comm['run']+1;
                updatePlayerCol($con, 'runsGiven', $comm['bowler'], $runsGiven, $mid);

                //Updating batting team score.
                if($matchInfo['t1batting']==1){
                    $t1runs=$t1runs+$comm['run'];
                    updateMatchCol($con, 't1runs', $t1runs, $mid);

                    $t1wkts++;
                    updateMatchCol($con, 't1wkts', $t1wkts, $mid);
                }else{
                    $t2runs=$t2runs+$comm['run'];
                    updateMatchCol($con, 't2runs', $t2runs, $mid);

                    $t2wkts++;
                    updateMatchCol($con, 't2wkts', $t2wkts, $mid);
                }


            }elseif($islegal==0){
                 //bowlCAT == Wide, NoBall
                $msg1 = '<p><b>'.$over.'</b>  '.$bowler.' to '.$strike.', '.$bowlCat.', '.$run.'. </p>';
                $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));

                $toss = $matchInfo['toss'];
                $toss = $toss[0];

                if($teamBatting == $matchInfo['tid1']){ // If team1 is batting.
                    if($runsThrough == 'bye' || $runsThrough == 'lbye'){
                        $tid2extras = $tid2extras+$comm['run']+1;
                        updateMatchCol($con, 't2extrasGiven', $tid2extras, $mid);

                        $bowlerExtras = $bowlerExtras+$comm['run']+1;
                        updatePlayerCol($con, 'extrasGiven', $comm['bowler'], $bowlerExtras, $mid);
                    }else{
                        $tid2extras = $tid2extras+1;
                        updateMatchCol($con, 't2extrasGiven', $tid2extras, $mid);

                        $playerRuns = $playerRuns+$comm['run'];
                        updatePlayerCol($con, 'runsScored', $comm['strike'], $playerRuns, $mid);

                        //Change Strike.
                        if($comm['run']%2!=0){
                            updatePlayerCol($con, 'batStatus', $comm['strike'], '12', $mid);
                            updatePlayerCol($con, 'batStatus', $comm['nstrike'], '11', $mid);
                        }
                    }
                    

                    $t1runs =  $t1runs+$comm['run']+1;
                    updateMatchCol($con, 't1runs', $t1runs, $mid);
                   
                    

                }else{ // If team2 is batting.
                    if($runsThrough == 'bye' || $runsThrough == 'lbye'){
                        $tid1extras = $tid2extras+$comm['run']+1;
                        updateMatchCol($con, 'tid1extrasGiven', $tid1extras, $mid);

                        $bowlerExtras = $bowlerExtras+$comm['run']+1;
                        updatePlayerCol($con, 'extrasGiven', $comm['bowler'], $bowlerExtras, $mid);
                    }else{
                        $tid1extras = $tid1extras+1;
                        updateMatchCol($con, 'tid2extras', $tid1extras, $mid);

                        $playerRuns = $playerRuns+$comm['run'];
                        updatePlayerCol($con, 'runsScored', $comm['strike'], $playerRuns, $mid);

                        //Change Strike.
                        if($comm['run']%2!=0){
                            updatePlayerCol($con, 'batStatus', $comm['strike'], '12', $mid);
                            updatePlayerCol($con, 'batStatus', $comm['nstrike'], '11', $mid);
                        }
                    }

                    $t2runs =  $t2runs+$comm['run']+1;
                    updateMatchCol($con, 't2runs', $t2runs, $mid);
                }



                if($bowlCat != 'wide'){
                    $playerBowlsPlayed++;
                    updatePlayerCol($con, 'bowlsPlayed', $comm['strike'], $playerBowlsPlayed, $mid);
                }

                $runsGiven = $runsGiven+$comm['run']+1;
                updatePlayerCol($con, 'runsGiven', $comm['bowler'], $runsGiven, $mid);
                // updatePlayerCol($con, 'batStatus', $comm['strike'], '11', $mid);
                // updatePlayerCol($con, 'batStatus', $comm['nstrike'], '12', $mid);

            }elseif($islegal==1){

                if($runsThrough=='bye'){
                    $runsThrough = 'byes';

                    if($teamBatting == $matchInfo['tid1']){ 
                        $tid2extras = $tid2extras+$comm['run']+1;
                        updateMatchCol($con, 't2extrasGiven', $tid2extras, $mid);
                    }else{
                        $tid1extras = $tid1extras+$comm['run']+1;
                        updateMatchCol($con, 't1extrasGiven', $tid1extras, $mid);
                    }

                }elseif($runsThrough=='lbye'){
                    $runsThrough = 'leg bye';

                    if($teamBatting == $matchInfo['tid1']){ 
                        $tid2extras = $tid2extras+$comm['run']+1;
                        updateMatchCol($con, 't2extrasGiven', $tid2extras, $mid);
                    }else{
                        $tid1extras = $tid1extras+$comm['run']+1;
                        updateMatchCol($con, 't1extrasGiven', $tid1extras, $mid);
                    }

                }else{
                    $runsThrough='';
                    $playerRuns = $playerRuns+$comm['run'];
                    
                    

                    echo 'Run of Player: '.$playerRuns;
                    updatePlayerCol($con, 'runsScored', $comm['strike'], $playerRuns, $mid);

                    if($comm['run']==4){
                        $player4sCount++;
                        updatePlayerCol($con, '4s', $comm['strike'], $player4sCount, $mid);
                    }else if($comm['run']==6){
                        $player6sCount++;
                        updatePlayerCol($con, '6s', $comm['strike'], $player6sCount, $mid);
                    }
                    
                    //Change Strike.
                    if($comm['run']%2!=0){
                        updatePlayerCol($con, 'batStatus', $comm['strike'], '12', $mid);
                        updatePlayerCol($con, 'batStatus', $comm['nstrike'], '11', $mid);
                    }
                }

                $msg1 = '<p><b>'.$over.'</b>  '.$bowler.' to '.$strike.', '.$run.' '.$runsThrough.'.</p>';
                $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
                if($run=='no run'){
                    $msg1 = '<p><b>'.$over.'</b>  '.$bowler.' to '.$strike.', '.$run.'.</p>';
                    $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
                }

            
                
                $playerBowlsPlayed++;

                $playerBowlsBowled = OversToBalls($playerBowlsBowled)+1;
                $playerBowlsBowled = ballsToOvers($playerBowlsBowled);
                
                updatePlayerCol($con, 'bowlsPlayed', $comm['strike'], $playerBowlsPlayed, $mid);
                updatePlayerCol($con, 'oversBowled', $comm['bowler'], $playerBowlsBowled, $mid);

                // //updating strikes
                // updatePlayerCol($con, 'batStatus', $comm['strike'], '11', $mid);
                // updatePlayerCol($con, 'batStatus', $comm['nstrike'], '12', $mid);

                $runsGiven = $runsGiven+$comm['run'];
                updatePlayerCol($con, 'runsGiven', $comm['bowler'], $runsGiven, $mid);

                if($matchInfo['t1batting']==1){
                    $t1runs=$t1runs+$comm['run'];
                    updateMatchCol($con, 't1runs', $t1runs, $mid);
                }else{
                    $t2runs=$t2runs+$comm['run'];
                    updateMatchCol($con, 't2runs', $t2runs, $mid);
                }
            }


            //Update Match status and Bowler status-- Over Break
            if($islegal==1 && $bowl%6==0){
                updateMatchCol($con, 'status', '41', $mid);
                updatePlayerCol($con, 'bowlStatus', $bowler, '22', $mid);
            }

            //Update Match status -- Batsman Wicket Break
            if($isOut==1){
                updateMatchCol($con, 'status', '42', $mid);
            }


            if($matchInfo['t1batting']==1){
                updateMatchCol($con, 't1oversDone', $over, $mid);
            }else{
                updateMatchCol($con, 't2oversDone', $over, $mid);
            }

            InsertCommentary($con, $mid, $msg1);
            

        }elseif($comm['step']=='nextBat'){
            $nextBat = getInfoByPlayerId($comm['nextBat'], $con)['firstname'].' '.getInfoByPlayerId($comm['nextBat'], $con)['lastname'];
            $mid = $comm['mid'];
            $outPlayer = $comm['outPlayer'];

            $msg1 = '<p><b>'.$nextBat.', '.getInfoByPlayerId($comm["nextBat"], $con)["battingStyle"].' comes to the crease.</b></p>';
            $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            //Match Status-- After wicket 42 --> 4
            updateMatchCol($con, 'status', '4', $mid);

            //updating strikes
            updatePlayerCol($con, 'batStatus', $comm['strike'], '11', $mid);
            updatePlayerCol($con, 'batStatus', $comm['nstrike'], '12', $mid);

            updatePlayerCol($con, 'batStatus', $outPlayer, '10', $mid);

            InsertCommentary($con, $mid, $msg);    
        }

        elseif($comm['step']=='nextOver'){

            $nextBowlerID = $comm['nextBowler'];
            $nextBowlerName = $comm['nextBowlerName'];
            $mid = $comm['mid'];
            $matchInfo = getInfoByMatchId($mid, $con);

            $firstInning = ($matchInfo['toss']==11 && $matchInfo['t1batting']==1) || ($matchInfo['toss']==21 && $matchInfo['t1batting']==0);

            $currBowler = $comm['currBowler'];
            $currBowlerName = getInfoByPlayerId($currBowler, $con)['firstname'].' '.getInfoByPlayerId($currBowler, $con)['lastname'];
            $currBowlerOvers = getInfoByPlaying11Id($currBowler, $con, $mid)['oversBowled'];
            $currBowlerRuns = getInfoByPlaying11Id($currBowler, $con, $mid)['runsGiven'];
            $currBowlerwkts = getInfoByPlaying11Id($currBowler, $con, $mid)['wicketsTaken'];
            $currBowlermaiden = getInfoByPlaying11Id($currBowler, $con, $mid)['maiden'];

            $strike = $comm['strike'];
            $strikename = getInfoByPlayerId($strike, $con)['firstname'].' '.getInfoByPlayerId($strike, $con)['lastname'];
            $strikeruns = getInfoByPlaying11Id($strike, $con, $mid)['runsScored'];
            $strikebowlsPlayed = getInfoByPlaying11Id($strike, $con, $mid)['bowlsPlayed'];

            $nstrike = $comm['nstrike'];
            $nstrikename = getInfoByPlayerId($nstrike, $con)['firstname'].' '.getInfoByPlayerId($nstrike, $con)['lastname'];
            $nstrikeruns = getInfoByPlaying11Id($nstrike, $con, $mid)['runsScored'];
            $nstrikebowlsPlayed = getInfoByPlaying11Id($nstrike, $con, $mid)['bowlsPlayed'];

            $matchSize = $matchInfo['match_size'];


            $nextBowlerStatus = getInfoByPlaying11Id($nextBowlerID, $con, $mid)['bowlStatus'];
            // $BowlerStatus = getInfoByPlaying11Id($nextBowlerID, $con, $mid)['bowlStatus'];

            if($nextBowlerStatus=='22'){ // Old Bowler.(Bowled atleast one over)
                $msg1 = '<p><b>'.$nextBowlerName.', '.getInfoByPlayerId($nextBowlerID, $con)["bowlingStyle"].' is back into the attack.</b></p>';
                $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            }else{ // New Bowler
                $msg1 = '<p><b>'.$nextBowlerName.', '.getInfoByPlayerId($nextBowlerID, $con)["bowlingStyle"].' comes into the attack.</b></p>';
                $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            }

            //Update Next Bowler.            
            updatePlayerCol($con, 'bowlStatus', $nextBowlerID, '21', $mid);



            if($matchInfo['t1batting']==1){
                $score = $matchInfo['t1runs'];
                $wkts = $matchInfo['t1wkts'];
                $completedOver = floor($matchInfo['t1oversDone'])+1;
                // $rr = -1;
            }else{
                $t1score = $matchInfo['t1runs'];
                $score = $matchInfo['t2runs'];
                $wkts = $matchInfo['t2wkts'];
                $completedOver = floor($matchInfo['t2oversDone'])+1;
                
            }


            if($firstInning){ // First Inning Going on.
                $rr = -1;
            }else{
                $remOver = $matchSize - $completedOver;
                $target = ($t1score-$score)+1 ;
                $rr = round($target/$remOver, 2);
            }



            $crr = round($score/$completedOver, 2);
            
            if(!$firstInning){ //2nd Inning going on.
                $rrmsg = 'RR: '.$rr;
            }else{
                $rrmsg = '';
            }

            $currBowlerOvers = floor($currBowlerOvers)+1;
            //Over Summary
            $msg1 .= '<hr>
                    <div class="overSummary my-2">
                        <div class="row r1 justify-content-between">

                            <div class="col-3">
                            <span>OVER '.$completedOver.'</span>

                            </div>
                            <div class="col-3">
                                <span><b>IND '.$score.'-'.$wkts.'</b></span>

                            </div>
                            <div class="w-100"></div>
                            <div class="col-3"></div>
                            <div class="col-3">
                                <span>CRR: '.$crr.' '.$rrmsg.'</span>
                            </div>
                        
                        </div>
                        
                        <div class="line"></div>

                        <div class="row r2 justify-content-between">
                            <div class="col-4">
                            <span>'.$strikename.' '.$strikeruns.'('.$strikebowlsPlayed.')</span>

                            </div>
                            <div class="col-3">
                                <span>'.$currBowlerName.'</span>

                            </div>
                            <div class="w-100"></div>
                            <div class="col-4">
                                <span>'.$nstrikename.' '.$nstrikeruns.'('.$nstrikebowlsPlayed.')</span>
                            </div>
                            <div class="col-3">
                                <span>'.$currBowlerOvers.'-'.$currBowlermaiden.'-'.$currBowlerRuns.'-'.$currBowlerwkts.'</span>
                            </div>
                        
                        </div>
                    </div>';
            $msg = json_encode(array("commentary"=> $msg1,"type"=> 'comm'));
            //Updating match status--- after over break
            updateMatchCol($con, 'status', '4', $mid);

            //updating strikes
            updatePlayerCol($con, 'batStatus', $strike, '11', $mid);
            updatePlayerCol($con, 'batStatus', $nstrike, '12', $mid);

            //Updating current bowler status.
            updatePlayerCol($con, 'bowlStatus', $currBowler, '22', $mid);

            InsertCommentary($con, $mid, $msg1);

        }elseif($comm['step']=='inningBreak'){
            $inning = $comm['inning'];
            $mid = $comm['mid'];
            $matchInfo = getInfoByMatchId($mid, $con);

            if($matchInfo['t1batting']==1){
                updateMatchCol($con, 't1batting', 0, $mid);
            }else{
                updateMatchCol($con, 't1batting', 1, $mid);
            }
            updateMatchCol($con, 'status', '40', $mid);


            $msg1 .= '<hr>
                    <div class="overSummary my-2">
                        <div class="row r1 justify-content-between">

                            <div class="col-3">
                            <span>OVER '.$completedOver.'</span>

                            </div>
                            <div class="col-3">
                                <span><b>IND '.$score.'-'.$wkts.'</b></span>

                            </div>
                            <div class="w-100"></div>
                            <div class="col-3"></div>
                            <div class="col-3">
                                <span>CRR: '.$crr.' '.$rrmsg.'</span>
                            </div>
                        
                        </div>
                        
                        <div class="line"></div>

                        <div class="row r2 justify-content-between">
                            <div class="col-4">
                            <span>'.$strikename.' '.$strikeruns.'('.$strikebowlsPlayed.')</span>

                            </div>
                            <div class="col-3">
                                <span>'.$currBowlerName.'</span>

                            </div>
                            <div class="w-100"></div>
                            <div class="col-4">
                                <span>'.$nstrikename.' '.$nstrikeruns.'('.$nstrikebowlsPlayed.')</span>
                            </div>
                            <div class="col-3">
                                <span>'.$currBowlerOvers.'-'.$currBowlermaiden.'-'.$currBowlerRuns.'-'.$currBowlerwkts.'</span>
                            </div>
                        
                        </div>
                    </div>';

        }elseif($comm['step']=='scorecard'){
            $strike = $comm['strike'];
            $nstrike = $comm['nstrike'];
            $strikeName = getInfoByPlayerId($strike, $con)['firstname'].' '.getInfoByPlayerId($strike, $con)['lastname'];
            $nstrikeName = getInfoByPlayerId($nstrike, $con)['firstname'].' '.getInfoByPlayerId($nstrike, $con)['lastname'];

            $bowler = $comm['bowler'];
            $bowlerName = getInfoByPlayerId($bowler, $con)['firstname'].' '.getInfoByPlayerId($bowler, $con)['lastname'];

            $mid = $comm['mid'];
            $matchInfo = getInfoByMatchId($mid, $con);

            $strinfo = getInfoByPlaying11Id($strike, $con, $mid);
            $nstrinfo = getInfoByPlaying11Id($nstrike, $con, $mid);
            $bowlerInfo = getInfoByPlaying11Id($bowler, $con, $mid);

            $strRuns = $strinfo['runsScored'];
            $strballsPlayed = $strinfo['bowlsPlayed'];
            $strfours = $strinfo['4s'];
            $strsix = $strinfo['6s'];

            $strikeInfo = json_encode(array("pid"=>$strike,"name"=> $strikeName, "runs"=> $strRuns,"ballsPlayed"=> $strballsPlayed, "fours"=> $strfours, "six"=> $strsix));

            $nstrRuns = $nstrinfo['runsScored'];
            $nstrballsPlayed = $nstrinfo['bowlsPlayed'];
            $nstrfours = $nstrinfo['4s'];
            $nstrsix = $nstrinfo['6s'];

            $nstrikeInfo = json_encode(array("pid"=>$nstrike, "name"=> $nstrikeName, "runs"=> $nstrRuns,"ballsPlayed"=> $nstrballsPlayed, "fours"=> $nstrfours, "six"=> $nstrsix));

            $oversBowled = $bowlerInfo['oversBowled'];
            $wktsTaken = $bowlerInfo['wicketsTaken'];
            $maiden = $bowlerInfo['maiden'];
            $runsGiven = $bowlerInfo['runsGiven'];
            $extrasGiven = $bowlerInfo['extrasGiven'];

            $bowlerInfo = json_encode(array("pid"=>$bowler, "name"=> $bowlerName, "wkts"=> $wktsTaken,"maiden"=> $maiden, "runs"=> $runsGiven, "balls"=>$oversBowled, "extras"=> $extrasGiven));

            if($matchInfo['t1batting']==1){
                $runs = $matchInfo['t1runs'];
                $wkts = $matchInfo['t1wkts'];
            }else{
                $runs = $matchInfo['t2runs'];
                $wkts = $matchInfo['t2wkts'];
            }

            

            $msg = json_encode(array("strike"=> $strikeInfo,"nstrike"=> $nstrikeInfo, "bowler"=> $bowlerInfo, "runs"=> $runs, "wkts"=> $wkts ,"ballCat"=> "legal", "type"=> "scorecard"));
        }        

        
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
        
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}