<?php
session_start();

    include("connect.php");
    include("functions.php");

    $valid_input = true;
    $correct_information = true;

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        //something was posted
        $username =  mysqli_real_escape_string($conn,$_POST['username']);
        $password =  mysqli_real_escape_string($conn,$_POST['password']);

        

        if(!empty($username) && !empty($password))
        {

            //read from database
            
            $query = "select * from mailbox where username = ? limit 1";

            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $query)) {
                echo "MYSQL error";
            } else {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
            }

            $result =  mysqli_stmt_get_result($stmt);
           
                if($result && mysqli_num_rows($result) > 0)
                {
                        $user_data = mysqli_fetch_assoc($result);

                        if($user_data['password'] === $password)
                        {
                            $_SESSION['username'] = $user_data['username'];
                         header("Location: editprofile.php");

                        }
                }          
           $correct_information = false;
        }else{   
            $valid_input = false;  
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="css/main.css">
</head>

<body>

<div class="sign-up-form">
    <h1>Login now</h1>
    <form method="post" class="input-box">
        <input type="text" placeholder="Your username" name="username">
        <input type="text" placeholder="Your password" name="password">
        <hr><div class="red">
        <?php if (!$correct_information)
                    echo "<span> The username or password is incorrect. </span>"  ?>

        <?php if (!$valid_input)
                    echo "<span> Enter only permitted symbols. </span>"  ?>
           </div>
        <br>
        <button type="submit" class="signup-btn">Login</button>
        <a href="signup.php">Sign up</a>
        
    </form>

</div>
    
</body>
</html>