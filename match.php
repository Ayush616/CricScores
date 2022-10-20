<?php include "functions.php"; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Domine:wght@500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Domine:wght@500&family=Slabo+27px&display=swap" rel="stylesheet">

<link rel="stylesheet" href="match.css">


<body>

<?php
        
            $mid = $_GET['mid'];
            $tid1 = getInfoByMatchId($mid, $con)['tid1'];
            $tid2 = getInfoByMatchId($mid, $con)['tid2'];
        
            $tname1 = getInfoByTeamId($tid1, $con)['tname'];
            $tname2 = getInfoByTeamId($tid2, $con)['tname'];

            $matchInfo = getInfoByMatchId($mid, $con);
            $firstInning = ($matchInfo['toss']==11 && $matchInfo['t1batting']==1) || ($matchInfo['toss']==21 && $matchInfo['t1batting']==0);



            $tquery = mysqli_query($con, "SELECT * FROM playing11 WHERE batStatus=11 AND match_id = '$mid'");
            $tquery = mysqli_fetch_assoc($tquery);

            $batTeamId = $tquery['team_id'];

            if($batTeamId==$tid1){
                $bowlTeamId = $tid2;
            }else{
                $bowlTeamId = $tid1;
            }

            $BatnickName = getInfoByTeamId($batTeamId, $con)['nickName'];

            //Current Score
            if($batTeamId==$tid1){
                $currScore = getInfoByMatchId($mid, $con)['t1runs'];
                $wkts = getInfoByMatchId($mid, $con)['t1wkts'];
                $overs = getInfoByMatchId($mid, $con)['t1oversDone'];
            }else{
                $currScore = getInfoByMatchId($mid, $con)['t2runs'];
                $wkts = getInfoByMatchId($mid, $con)['t2wkts'];
                $overs = getInfoByMatchId($mid, $con)['t2oversDone'];
            }
        ?>


<!------Navbar---------->
<input type="hidden" id="mid" value="<?php echo $mid;?>">
<navbar class="navbar navbar-expand-xl navbar-light bg-white sticky-top p-0">
   <div class="container custom-container">
      
       <a class="navbar-brand mr-5" href="#" style="margin-left: -30px; margin-bottom: -10px"><img width="75"src="logo.png" alt=""></a>
       <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#responsive">
           <span class="navbar-toggler-icon"></span>
       </button>
       
       <div class="collapse navbar-collapse" id="responsive">
           <ul class="navbar-nav mr-auto">
               <li class="nav-item" id="current">
                   <a id="index.php" href="" class="nav-link"><i class="fas fa-home fa-lg"></i> HOME</a>
               </li>
               <li class="nav-item">
                   <a href="#" class="nav-link"><i class="far fa-question-circle fa-lg"></i> SCORES</a>
               </li>
               <li class="nav-item">
                   <a href="#" class="nav-link current"><i class="fas fa-bell fa-lg"></i> STATS</a>
               </li>
               <li class="nav-item">
                   <a href="#" class="nav-link current"><i class="fas fa-bell fa-lg"></i> ABOUT US</a>
               </li>
               <li class="nav-item">
                   <a href="#" class="nav-link current"><i class="fas fa-bell fa-lg"></i> CONTACT US</a>
               </li>
               
               
               
               <form class="form-inline my-2 my-lg-0" style="margin-left:100px">
                    <input class="form-control mr-sm-2" style="width: 200px;" type="search" placeholder="Search Players or teams" aria-label="Search">
                  
                </form>
                
                <li class="nav-item dropdown">
                   <a class="nav-link dropdown-toggle btn btn-sm mt-2 mb-2 text-white" style="height:35px; background-color:purple" href="#" id="navbardrop" data-toggle="dropdown">Log In</a>
                    
                    <div class="dropdown-menu" style="">
                           <hr>
                            <a class="dropdown-item my-1" data-toggle="modal" data-target="#login">Host</a>
                            <a class="dropdown-item my-1" href="#">Admin</a>
                            <hr>
                      </div>
                      
  
               </li>
               <li class="nav-item">
                        <a href="#" class="m-2 btn btn-secondary">Register</a>
                    </li>
              
           </ul>
           
       </div>
       
   </div>
    
</navbar>
 
 
 <div class="container p-3 my-3 border w-60" style="background: #f0f5f5; overflow:auto;">
     
     <h4><?php echo $tname1;?> v/s <?php echo $tname2;?></h4>
     <hr color="blue">
     
     <div class="container">
        <div class="totalRuns mb-3 slabo">
            <span class="h3"><?php echo $BatnickName." ".$currScore."/".$wkts?></span> <span class="runRate text-muted">CRR: <?php if($overs==0){echo $overs; } else{echo round($currScore/$overs, 2);} ?>
        <?php
            if($firstInning==0){
            ?>
            RR: <?php if($overs==0){echo $overs; } else{echo round($currScore/$overs, 2);} ?></span>
            <?php }?>

        </div>

        <div id="mini-scorecard" class="domine">

        <?php 
            $batq = mysqli_query($con, "SELECT * FROM playing11 WHERE (batStatus = 11 OR batStatus = 12) AND match_id='$mid'");
            $bowlq = mysqli_query($con, "SELECT * FROM playing11 WHERE (bowlStatus = 21) AND match_id='$mid'");
            // $qstrike = mysqli_query($con, "SELECT * FROM playing11 WHERE batStatus = 11 AND match_id='$mid'");
        ?>
            <table class="table">
             <thead class="thead-light">
                <tr>
                    <th scope="col">Batsman</th>
                    <th scope="col">R</th>
                    <th scope="col">B</th>
                    <th scope="col">4</th>
                    <th scope="col">6</th>
                    <th scope="col">S R</th>
                </tr>
               </thead> 
               <?php 
               while($prow = mysqli_fetch_assoc($batq)){
                   if($prow['bowlsPlayed']==0){
                       $strikeRate=0;
                   }else $strikeRate = ($prow['runsScored']/$prow['bowlsPlayed'])*100;
                
               ?>
                <tr>
                    <td class="<?php echo $prow['player_id']; ?>-batname"><?php echo $prow['player_name']; if($prow['batStatus']==11) echo '<span class="text-danger bolder"> *</span>';?></td>
                    <td class="<?php echo $prow['player_id']; ?>-runs"><?php echo $prow['runsScored']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-bowls"><?php echo $prow['bowlsPlayed']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-4s"><?php echo $prow['4s']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-6s"><?php echo $prow['6s']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-sr"><?php echo round($strikeRate,2); ?></td>
                </tr>
               <?php }?>

                <thead class="thead-light">
                <tr>
                    <th scope="col">Bowler</th>
                    <th scope="col">O</th>
                    <th scope="col">M</th>
                    <th scope="col">R</th>
                    <th scope="col">W</th>
                    <th scope="col">ECO</th>
                </tr>
               </thead>
               <?php 
               while($prow = mysqli_fetch_assoc($bowlq)){

                if($prow['oversBowled']==0){
                    $economy=0;
                }else $economy = ($prow['runsGiven']/$prow['oversBowled']);
                
               ?>
                <tr>
                    <td class="<?php echo $prow['player_id']; ?>-bowlname"><?php echo $prow['player_name']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-overs"><?php echo $prow['oversBowled']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-maiden"><?php echo $prow['maiden']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-runs"><?php echo $prow['runsGiven']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-wkts"><?php echo $prow['wicketsTaken']; ?></td>
                    <td class="<?php echo $prow['player_id']; ?>-eco"><?php echo round($economy,2); ?></td>
                </tr>
               <?php }?>
            </table>

        </div>
     </div>
     <hr>

     
     <div class="container commentary domine">

        <?php 
            $q = mysqli_query($con, "SELECT * FROM commentaries WHERE mid = '$mid' ORDER BY id DESC");

            if(mysqli_num_rows($q)>0){
                while($comm = mysqli_fetch_assoc($q)){
                    echo $comm['commentaries'];
                }
            }else echo "<p class='text-dark bg-warning'>Match is yet to start.<p>";
        
        ?>
         <?php 
         echo '<h5 class="lead"><u>'.$tname1." Squad: </h5></u>";
         $q= "SELECT * FROM players WHERE tid = '$tid1'";
         $q = mysqli_query($con, $q);
         
         $t1list = '';
         while($row = mysqli_fetch_assoc($q)){
             $t1list.= $row['firstname'].' '.$row['lastname'].', ';
         
         }
         $t1list = substr($t1list, 0, -1);
         
         
         echo $t1list."<br><br>";
         
         
         echo '<h5 class="lead"><u>'.$tname2." Squad: </h5></u>";
         $q= "SELECT * FROM players WHERE tid = '$tid2'";
         $q = mysqli_query($con, $q);
         
         $t2list = '';
         while($row = mysqli_fetch_assoc($q)){
             $t2list.= $row['firstname'].' '.$row['lastname'].', ';
         
         }
         $t2list = substr($t2list, 0, -1);
         
         
         echo $t2list."<br>";
             
             
         ?>
         
         
         
         
     </div>
     
     
     
 </div>
 
 <div class="modal" tabindex="-1" role="dialog" id="login">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Already registered? Please Sign in..</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body m-3">
                <p>Enter credentials to login to Host Portal</p>
                
                  
                      <form action="redirect.php" method="POST" class="form-group row m1 justify-content-center mt-4">
                    
                                    <p class="small">Login as a Host</p>

                                    <input type="email" name="email" class="form-control mb-2" id="exampleInputEmail1"
                                     aria-describedby="emailHelp" placeholder="Enter your Username">

                                     <input type="password" name="pass" class="form-control mb-2" id="exampleInputPassword1" 
                                     placeholder="Enter your Password">
                                    
                                     <input type="submit" value="Login" name="login" class="btn mt-1 btn-block btn-dark btn-sm large">
                                     
                          </form>
                          <div class="row m1 justify-content-between">
                              <div class="col colso">
                                <input type="checkbox" class="small" id="check"> <label class="form-check-label small" for="check">Check me out</label>
                              </div>
                              <div class="col colso">
                                <a href="" class="small">Forgot Password?</a>
                              </div>
                          </div>
                 <div class="row or m2 justify-content-center mt-2">----OR----</div>             
                 <div class="row m2 justify-content-center mt-2">
                  <button class="btn btn-danger btn-sm"><i class="fab fa-google-plus"></i>  Sign Up with Gmail</button>
                  </div>
                
              </div>
              <div class="modal-footer row justify-content-start ml-3">
                <p class="small ">Note: "Sign in with Gmail" option is only available for clients.</p>
              </div>
            </div>
          </div>
        </div>




 
 <footer class="page-footer font-small text-light fixed-bottom mb-3" style="background-color:purple">

  <!-- Copyright -->
  <div class="footer-copyright text-center py-3 font-weight-lighter">© 2020 Copyright: <span class="font-weight-bold">Ayush and Team</span></div>
  
  <!-- Copyright -->

</footer>
  
   
</body>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    
$(document).ready(function() {
    var conn = new WebSocket('ws://localhost:8080');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    var mid = $('#mid').val();
    conn.onmessage = function(e) {
        var comm = JSON.parse(e.data);
        if(comm.type=="comm"){
            $( ".commentary" ).prepend(comm.commentary);
        }
        else if(comm.type=="scorecard"){
            console.log("Scorecard")
            var strike = JSON.parse(comm.strike);
            var nstrike = JSON.parse(comm.nstrike);
            var bowler = JSON.parse(comm.bowler);
            var spid = strike.pid;
            var nspid = nstrike.pid;
            var bpid = bowler.pid;
            $('#mini-scorecard '+'.'+spid+'-'+'batname').html(strike.name+'<span class="text-danger bolder"> *</span>');
            $('#mini-scorecard '+'.'+spid+'-'+'runs').html(strike.runs);
            $('#mini-scorecard '+'.'+spid+'-'+'bowls').html(strike.ballsPlayed);
            $('#mini-scorecard '+'.'+spid+'-'+'4s').html(strike.fours);
            $('#mini-scorecard '+'.'+spid+'-'+'6s').html(strike.six);
            $('#mini-scorecard '+'.'+spid+'-'+'sr').html(strike.six);

            $('#mini-scorecard '+'.'+nspid+'-'+'batname').html(nstrike.name);
            $('#mini-scorecard '+'.'+nspid+'-'+'runs').html(nstrike.runs);
            $('#mini-scorecard '+'.'+nspid+'-'+'bowls').html(nstrike.ballsPlayed);
            $('#mini-scorecard '+'.'+nspid+'-'+'4s').html(nstrike.fours);
            $('#mini-scorecard '+'.'+nspid+'-'+'6s').html(nstrike.six);
            $('#mini-scorecard '+'.'+nspid+'-'+'sr').html(strike.six);

            $('#mini-scorecard '+'.'+bpid+'-'+'bowlname').html(bowler.name);
            $('#mini-scorecard '+'.'+bpid+'-'+'wkts').html(bowler.wkts);
            $('#mini-scorecard '+'.'+bpid+'-'+'maiden').html(bowler.maiden);
            $('#mini-scorecard '+'.'+bpid+'-'+'extras').html(bowler.extras);
            $('#mini-scorecard '+'.'+bpid+'-'+'runs').html(bowler.runs);
            $('#mini-scorecard '+'.'+bpid+'-'+'bowls').html(strike.balls);

        }

    };
    
    
    });
    
   
    
    
    
    </script>
</html>