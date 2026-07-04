<?php
session_start();
include "db.php";

$error = "";

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $res = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($res)>0){
        $user=mysqli_fetch_assoc($res);
        $_SESSION['user_id']=$user['id'];
        $_SESSION['role']=trim($user['role']);
        $_SESSION['department']=$user['department'];

        if($_SESSION['role']=="superadmin" || $_SESSION['role']=="admin"){
            header("Location: admin-dashboard.php");
        }else{
            header("Location: student-dashboard.php");
        }
        exit();
    }else{
        $error="Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BCRP4ZV204"></script>
<script>
window.dataLayer=window.dataLayer||[];
function gtag(){dataLayer.push(arguments);}
gtag('js',new Date());
gtag('config','G-BCRP4ZV204');
</script>
<title>Login | Grievance Portal</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="center">

<video autoplay muted loop playsinline id="bg-video">
<source src="videos/background.mp4" type="video/mp4">
</video>

<marquee behavior="alternate" direction="right" scrollamount="6" style="position:fixed;top:0;left:0;width:100%;background:#6a1b9a;color:#fff;font-size:18px;padding:10px;font-weight:bold;z-index:9999;">Speak Up for a Better Learning Environment</marquee>

<div class="college-header">
<div class="college-top">
<img src="logo.png" class="college-logo" alt="College Logo">
<h1 class="vignan-title">VIGNAN</h1>
</div>
<div class="college-subtitle">Institute of Technology and Science</div>
<p class="affiliation">(Autonomous | Affiliated to JNTUH | Approved by AICTE)</p>
<p class="naac">NAAC Accredited with A+ Grade</p>
</div>

<div class="login-box">
<h2 class="login-title">College Grievance Portal</h2>
<p class="login-subtitle">Secure Login for Students &amp; Administrators</p>

<?php if($error!=""){ ?>
<div style="background:red;color:white;padding:10px;border-radius:5px;margin-bottom:15px;text-align:center;">
<?php echo $error; ?>
</div>
<?php } ?>

<form method="POST" onsubmit="gtag('event','student_login');">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
<a href="register.html"><button type="button" class="admin-btn">Register</button></a>
</form>
</div>

</body>
</html>
