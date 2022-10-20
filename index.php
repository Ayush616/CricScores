<?php include "functions.php"; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<style>
    
    .navbar-nav > li{
          padding-left:5px;
          padding-right:5px;
        margin-bottom: -10px;
        }
        
        .navbar-nav > li{
          margin-left: 5px;
          margin-right:5px;
        }
        
    .navbar {
        -webkit-box-shadow: 0 8px 6px -6px #999;
        -moz-box-shadow: 0 8px 6px -6px #999;
        box-shadow: 0 8px 6px -6px #999;

        /* the rest of your styling */
    }
       
    #current{
            color: purple;
            border-bottom: 2px solid purple;
        }
    .scards{
    background: rgba(245,245,245, 0.8);
/*    padding: 10px;*/
/*    width: auto;*/
    border-radius: 10px;
    box-shadow: 5px 5px 8px #888888;
/*    margin: 10px;*/
}
    body { 
  background: url(bgh.jpg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}

    </style>

<body>
<!------Navbar---------->

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
 
 
 <div class="container p-3 my-3 border w-60" style="background: #f0f5f5">
     
     <h4>Upcoming Schedules</h4>
     <hr color="blue">
     
    <div class="mt-4 row">
            <?php
               $q = mysqli_query($con, "SELECT * FROM matches");
                
                if(mysqli_num_rows($q)){ ?>
                
                <?php $i=1; 
                                        
                                        
                                                              
            while($match = mysqli_fetch_assoc($q)){ 
             ?> 
                
             <div class="scards pb-1 m-2 col">
                <div class="p-1" style="background:silver; border-radius:10px 10px 20px 0px">
                <h5 class="">Match <?php echo $i; $i++;?></h5>
                <h6>Place: <?php echo $match['place']; ?></h6>
                </div>
                <div class="m-2">
                    <p><?php echo getInfoByTeamId($match['tid1'], $con)['tname']; ?> VS <?php echo getInfoByTeamId($match['tid2'], $con)['tname'];; ?></p>
                     <span><b>Match will start at </b> <?php getTimeString($match['datetime']); ?></span>
                 </div>
                 <button class="btn btn-secondary btn-sm" onclick="Enterintomatch(<?php echo $match['mid'];?>)" style="right:0">Enter</button>
             </div>
             
             <?php } ?>
             
           <?php }else{?>
           <div class="alert alert-info">No Matches Scheduled For now..</div>
           
           <?php } ?>
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
    
//$(document).ready(function() {
//    var conn = new WebSocket('ws://localhost:8080');
//    conn.onopen = function(e) {
//        console.log("Connection established!");
//    };
//
//    conn.onmessage = function(e) {
//        console.log(e.data);
//    };
//
//    $("#sub").click(function(){
//        var s = $("#post").val()
//       conn.send(s) 
//    });
//    
//    })
//    
    
    
    function Enterintomatch(mid){
        window.location.href = "match.php?mid="+mid;
    }
    
    
    
    
    </script>
</html>