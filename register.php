<?php
session_start();
include "db.php";

$message = "";
$redirect = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $roll_no = $_POST['roll_no'];
    $username = $_POST['username'];
    $branch = $_POST['branch'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if($password != $confirm){
        $message = "❌ Passwords do not match!";
        $redirect = "register.html";
    }
    else{

        $check = mysqli_query($conn,
        "SELECT * FROM users WHERE username='$username'");

        if(mysqli_num_rows($check) > 0){
            $message = "❌ Username already exists!";
            $redirect = "register.html";
        }
        else{

            mysqli_query($conn,
            "INSERT INTO users (roll_no, username, password, role, branch)
             VALUES ('$roll_no','$username','$password','student','$branch')");

            $message = "✅ Registration Successful!";
            $redirect = "login.html";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Message</title>
<link rel="stylesheet" href="style.css">
</head>

<body style="margin:0;">

<div class="success-modal">
    <div class="success-box">
        <h2><?php echo $message; ?></h2>
        <button onclick="goNext()">OK</button>
    </div>
</div>

<script>
function goNext(){
    window.location.href = "<?php echo $redirect; ?>";
}
</script>

</body>
</html>