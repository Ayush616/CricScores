<?php include 'db/connection.php';

session_start();
    date_default_timezone_set('Asia/Kolkata');
    include 'functions.php';

$name = $_SESSION['username'];
$id = $_SESSION['id'];

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="http://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="hostmain.css">
<link href="rome-master/dist/rome.css" rel="stylesheet">



</head>
<body>
  
  <navbar class="navbar navbar-expand-xl navbar-light bg-white sticky-top p-0" style="">
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
  
   <div class="container p-3 my-3 border w-60" style="background: #f0f5f5">
   <h3>Hello <?php echo $name; ?>, Welcome to your Host Portal.</h3>
   
            <div id="navigator">
           <button class="btn" id="tinfo">Add Team</button>
           <button class="btn" id="pinfo">Add Players</button>
           <button class="btn" id="sinfo">Schedule a Match</button>
           <button class="btn" id="hostbtn">Host a Match</button>
            </div> 
     <div class="container">
          <div class="row">

       

   <div class="col-7">
   
   
   <!--Schedule Match --------->
   
   
   <div id="schedform" class="" style="display: none">
            <form action="" method="post" id="sf">
                   <div class="form-group">
                            <label for="steam1">Team1</label>
                        
                            <select class="form-control" id="steam1" name="steam1" value="Batsman">
                               
                               <?php 
                                
                        
                                $q = mysqli_query($con, "SELECT * FROM team WHERE hostid = '".$_SESSION['id']."' AND active=1");
                                if(mysqli_num_rows($q)){
                                    
                                while($team = mysqli_fetch_assoc($q)){
                                ?>
                                <option value="<?php echo $team['tid']; ?>"><?php echo $team['tname']; ?></option>
                                <?php }
                                }else{ ?>
                                <option value="">You have not added any team</option>
                                <?php } ?>
                            </select>
                            
                            <h2>V/S</h2>
                            
                            <label for="steam2">Team2</label>
                            <select class="form-control" id="steam2" name="steam2" value="Batsman">
                               
                               <?php 
                                $_SESSION['id'] = 1; /// Need to remove
                                $q = mysqli_query($con, "SELECT * FROM team WHERE hostid = '".$_SESSION['id']."' AND active=1");
                                if(mysqli_num_rows($q)){
                                    
                                while($team = mysqli_fetch_assoc($q)){
                                ?>
                                <option value="<?php echo $team['tid']; ?>"><?php echo $team['tname']; ?></option>
                                <?php }
                                }else{ ?>
                                <option value="">You have not added any team</option>
                                <?php } ?>
                            </select>
                      </div>
                   
                      <button id="prsubmit" class="btn btn-sm btn-primary" onclick="scheduleAmatch()">Proceed</button><div class="error"> </div>
                      
                      
                      <div class="schedule">
                      
                      <!--Default date and time picker -->
                        <div class="form-group" id="example">
                            <label for="sdt">Select Date and Time</label>
                            <input type="text" id="dt" name="sdt" class="form-control"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="dtplace">Enter Place</label>
                            <input type="text" id="dtplace" name="dtplace" class="form-control" />
                        </div>
                        <input type="submit" id="schedsubmit" class="btn btn-sm btn-primary" value="Schedule">
                        
                    </div>

            </form>


        </div>
        <!--------------------------->
        
   
    <!------ADD TEAM----------->

        <div id="tform" class="" style="display: none">
           <p>Note: Atleast 15 players are required to make the team active.</p>
            <form action="" method="post" id="tf">
                    <div class="form-group">
                        <label for="tname">Team Name:</label>
                        <input type="text" class="form-control" placeholder="Enter Team Name" name= "tname" id="tname">
                      </div>
                      
                      <div class="form-group">
                        <label for="tmg">Team Manager:</label>
                        <input type="text" class="form-control" placeholder="Enter Team Manager name" name= "tmg" id="tmg">
                      </div>

                      <button type="submit" id="tsubmit" class="btn btn-sm btn-outline-success">Add Team</button>

            </form>


        </div>
        <!--------------------------->
    
    <!------ADD PLAYERS----------->

        <div id="pform" class="" style="display: none">
           <p>Note: Atleast 15 players are required to make the team active.</p>
            <form action="" method="post" enctype="multipart/formdata" style="display:" id="pf">
                   <div class="form-group">
                            <label for="type">Team:</label>
                        
                            <select class="form-control" id="team" name="team" value="Batsman">
                               
                               <?php 
                                $_SESSION['id'] = 1; /// Need to remove
                                $q = mysqli_query($con, "SELECT * FROM team WHERE hostid = '".$_SESSION['id']."'");
                                if(mysqli_num_rows($q)){
                                    
                                while($team = mysqli_fetch_assoc($q)){
                                ?>
                                <option value="<?php echo $team['tid']; ?>"><?php echo $team['tname']; ?></option>
                                <?php }
                                }else{ ?>
                                <option value="">You have not added any team</option>
                                <?php } ?>
                            </select>
                      </div>
                    <div class="form-group">
                        <label for="first">First name:</label>
                        <input type="text" class="form-control" placeholder="Enter First name" name= "first" id="first">
                      </div>

                      <div class="form-group">
                        <label for="last">Last name:</label>
                        <input type="text" class="form-control" placeholder="Enter Last name" name= "last" id="first">
                      </div>

                      <div class="form-group">
                            <label for="type">Player type:</label>
                            <select class="form-control" id="type" name="type" value="Batsman">
                                <option>Batsman</option>
                                <option>Bowler</option>
                                <option>All-Rounder</option>
                            </select>
                      </div>

                      <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="text" class="form-control" placeholder="Enter Date of Birth(DD/MM//YYYY)" name= "dob" id="dob">
                      </div>

                      <div class="form-group">
                        <input type="file" class="form-control" name= "img" id="img">
                      </div>

                      <button type="submit" id="psubmit" class="btn btn-sm btn-primary">Add Player</button>

            </form>


        </div>
        <!--------------------------->
        
        <!------Host a Match----------->


       
        <div id="hform" class="" style="display: none">
         
         <!--Schedule Cards-->
         <div>
            <?php
               $q = mysqli_query($con, "SELECT * FROM matches WHERE hostid = '".$_SESSION['id']."'");
                
                if(mysqli_num_rows($q)){ ?>
                
                <?php $i=1; 
                                        
                                        
                                                              
            while($match = mysqli_fetch_assoc($q)){ 
             ?> 
                
             <div class="scards pb-1 m-2">
                <div class="p-1" style="background:rgb(3,169,244); border-radius:10px 10px 20px 0px">
                <h5 class="">Match <?php echo $i; $i++;?></h5>
                <h6>Place: <?php echo $match['place']; ?></h6>
                </div>
                <div class="m-2">
                    <p><?php echo getInfoByTeamId($match['tid1'], $con)['tname']; ?> VS <?php echo getInfoByTeamId($match['tid2'], $con)['tname'];; ?></p>
                     <span><b>Match will start at </b> <?php getTimeString($match['datetime']); ?></span>
                     <button class="btn btn-secondary btn-sm ml-5" style="right:0" onclick="hostmatch(<?php echo $match['mid']; ?>)">Start Hosting this match</button>
                 </div>
             </div>
             
             <?php } ?>
             
           <?php }else{?>
           <div class="alert alert-info">No Matches Scheduled For now..</div>
           
           <?php } ?>
            </div>

        </div>
        
        <!--------------------------->

        </div>
        <div class="bd-rt"></div>
        <?php 
         
         $q = mysqli_query($con, "SELECT * FROM team WHERE hostid = '".$_SESSION['id']."'"); 
         
         if(mysqli_num_rows($q)>0){ ?>
        
        <div class="addedTeams col-5">
            <table class="table table-dark">
                <tr>
                   <th scope="col">S. No.</th>
                    <th scope="col">Team Name</th>
                </tr>
                <?php $i=1; while($team1 = mysqli_fetch_assoc($q)){ ;?>
                    <tr>
                       <td><?php ; echo $i; ?></td>
                        <td><?php echo $team1['tname']; $i++;?></td>
                    </tr>
                
                <?php } ?>
            </table>
            
        </div>
        
        <?php } ?>
    </div>
    </div>
</div>
</body>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="hostmain.js"></script>
   
  <script src="rome-master/dist/rome.js"></script>

    <script>
    rome(dt)
    </script>
</html>