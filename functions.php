<?php


function check_login($con){
    if(isset($_SESSION['username'])) //if the user exists in session
    {
        $username = $_SESSION['username'];
        $query = "select * from mailbox where username = '$username' limit 1";

        $result = mysqli_query($con,$query);
        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }     
    }
    //redirect to login
    header("Location: login.php");
 

}


