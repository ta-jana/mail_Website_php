<?php
session_start();

include("connect.php");
include("functions.php");

$name = "";
$username = "";
$password = "";

$usernameAvailable = true;
$valid_inputs = true;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //something was posted

    $name = mysqli_real_escape_string($conn,$_POST['name']) ;
    $username =  mysqli_real_escape_string($conn, $_POST['username']);
    $password =  mysqli_real_escape_string($conn,$_POST['password']);

    $_SESSION['username'] = $username;


    // fixed values
    $domain = "mailadress.online";
    $quota = 10;
    
    $length = strlen($username);

    //username unavailable
    $query1 = "SELECT username from mailbox where username = ? limit 1";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $query1)) {
        echo "MYSQL error";
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
    }

    $result =  mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $usernameAvailable = false;
    } else {

        if (!empty($username) && !empty($password) && !empty($name) && !is_numeric($username) && $length >= 3) {

            //first three letters
            $a = $username[0];
            $b = $username[1];
            $c = $username[2];

            $folderpath = "$domain/$a/$b/$c/$username";

            //save to databse
            $query = "INSERT INTO mailbox (name, username, password, maildir ,domain, quota ) VALUES (?,?,?,?,?,?)";

            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $query)) {
                echo "MYSQL error";
            } else {
                mysqli_stmt_bind_param($stmt, "sssssi", $name, $username, $password, $folderpath, $domain, $quota);
                mysqli_stmt_execute($stmt);
            }

            if (!file_exists('/var/vmail/' . $a . '')) {
                mkdir('/var/vmail/' . $a . '', 0777, true);
            }
            if (!file_exists('/var/vmail/' . $a . '/' . $b . '')) {
                mkdir('/var/vmail/' . $a . '/' . $b . '', 0777, true);
            }
            if (!file_exists('/var/vmail/' . $a . '/' . $b . '/' . $c . '')) {
                mkdir('/var/vmail/' . $a . '/' . $b . '/' . $c . '', 0777, true);
            }
            if (!file_exists('/var/vmail/' . $a . '/' . $b . '/' . $c . '/' . $username . '')) {
                mkdir('/var/vmail/' . $a . '/' . $b . '/' . $c . '/' . $username . '', 0777, true);
            }

            header("Location: editprofile.php");
        } else {
            $valid_inputs = false;
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

    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <div class="sign-up-form">
        <h1>Sign up now</h1>
        <form method="post" class="input-box">

        <?php if (!$valid_inputs)
                    echo "<span> Enter only permitted symbols. </span>"  ?>

            <input type="text" placeholder="Your Name" name="name" value="<?php echo $name; ?>">

            <div class="<?= $usernameAvailable ? 'input-box' : 'name-error' ?>">

                <input type="text" placeholder="Your Username" name="username" value="<?php echo $username; ?>">
                <?php if (!$usernameAvailable)
                    echo "<span> Username not available </span>"  ?>
            </div>
            <input type="text" placeholder="Your password" name="password" value="<?php echo $password; ?>">
            <hr>
            <br>
            <button type="submit" value="" class="signup-btn">
                <div>Sign up</div>
            </button>

        </form>
        <a href="login.php">Login</a>
    </div>



</body>

</html>