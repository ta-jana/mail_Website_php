<?php
session_start();

include("connect.php");
include("functions.php");

$user_data = check_login($conn);

$want_to_delete = false;

$password_change = false;


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    //something was posted

    $name =  mysqli_real_escape_string($conn,$_POST['name']);
    $password =  mysqli_real_escape_string($conn,$_POST['password']);
    $newpassword =  mysqli_real_escape_string($conn,$_POST['newpassword']);
    $oldpassword =  mysqli_real_escape_string($conn,$_POST['oldpassword']);
    $password3 =  mysqli_real_escape_string($conn,$_POST['password3']);

    $username = $user_data['username'];


    if ($user_data['password'] === $password || $user_data['password'] === $oldpassword || $user_data['password'] === $password3) {

        switch ($_POST['button']) {

            case 'newname':
                $queryName = "UPDATE mailbox SET name = ? WHERE username = ? ";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $queryName)) {
                    echo "MYSQL error";
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $name, $username);
                    mysqli_stmt_execute($stmt);
                    header("Location: editprofile.php");
                }
                break;

            case 'newpassword':
                $queryPassword = "UPDATE mailbox SET password = ? WHERE username = ? ";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $queryPassword)) {
                    echo "MYSQL error";
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $newpassword, $username);
                    mysqli_stmt_execute($stmt);
                    $password_change = true;
                }
                break;

            case 'delete':
                $want_to_delete = true;
                if (isset($_POST['delete_account'])) {
                    $queryDelete = "DELETE FROM mailbox WHERE username = ? ";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $queryDelete)) {
                        echo mysqli_error($conn);
                        
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $username);
                        mysqli_stmt_execute($stmt);
                        session_destroy();
                        header("Location: signup.php");
                    }

                
                }

                break;
        }
    }

    
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailt</title>

    <link rel="stylesheet" href="css/editprofile.css">
</head>

<body>

    <div class="sign-up-form">
        <h1>Edit information:</h1>
        <form method="post" class="input-box">
            <p> Your current name: <?php echo $user_data['name']; ?> </p>


            <input type="text" placeholder="New Name" name="name" value="">
            <input type="text" placeholder="Password" name="password" value="">


            <hr><br>
            <button type="submit" name="button" value="newname" class="signup-btn">
                <div>Change Name</div>
            </button>
            <br>

            <p> Your username: " <?php echo $user_data['username']; ?> " can't be changed. </p>
            <hr><br>

            <p> Change your password: </p>

            <?php if ($password_change == true)
                    echo "<span> Password changed successfully. </span>"  ?>

            <input type="text" placeholder="New password" name="newpassword" value="">

            <input type="text" placeholder="Old password" name="oldpassword" value="">
            <hr><br>
            <button type="submit" name="button" value="newpassword" class="signup-btn">
                <div>Change password</div>
            </button>

            
            <div class="<?= $checkbox_ticked ? 'unchecked' : 'checked' ?>">
                <div id="cb"> <input id="cbi" type="checkbox" name="delete_account" value="value1"></div>
                <div class="cb_text">
                <div class="<?= !$want_to_delete ? 'nored' : 'red' ?>">
                    <p>I am sure I want to delete my account.</p>
                </div>
                </div>
            </div>
            <input type="text" placeholder="Password" name="password3" value="">
            <hr><br>
            <button type="submit" name="button" value="delete" class="signup-btn">
                <div>Delete account</div>
            </button>
            <br>

            <a class="logout_link" href="logout.php">Log out</a>



        </form>
    </div>



</body>

</html>