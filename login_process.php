<?php
session_start();
include "db.php";

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users 
        WHERE username='$username' 
        AND password='$password'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {

    $row = mysqli_fetch_assoc($result);

    $_SESSION['user_id'] = $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];

    if ($row['role'] == "admin") {
        header("Location: admin-dashboard.php");
    } else {
        header("Location: student-dashboard.php");
    }

} else {
    echo "<script>
            alert('Invalid Username or Password');
            window.location.href='login.html';
          </script>";
}

?>