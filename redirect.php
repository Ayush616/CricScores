<?php include "db/connection.php";
    session_start();

if(array_key_exists("login", $_POST)){
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $pass = mysqli_real_escape_string($con, $_POST["pass"]);
    
    $query = "SELECT * FROM `host` WHERE `email`='$email'";
    
    $result="";
    if(!($result = mysqli_query($con, $query))){
        echo "Login query failed";
    }
    
    $row = mysqli_fetch_array($result);
        
    if(mysqli_num_rows($result)>0){
        
        if($row['password']==md5($pass)){
            $_SESSION['username'] = $row['firstname'];
            $_SESSION['id'] = $row['id'];
            header("location: hostmain.php");
            
        }else{
            echo "Wrong Password";
        }
        
    }
    

}

?>