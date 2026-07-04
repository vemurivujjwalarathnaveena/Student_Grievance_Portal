<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* ✅ AUTO CLEAR NOTIFICATION AFTER CHAT VISIT */
if(isset($_GET['read'])){
   mysqli_query($conn,
"UPDATE messages 
 SET is_read=1 
 WHERE receiver_id='".$student_id."'");
}

/* 🔴 UNREAD MESSAGE COUNT (FROM ADMIN) */
$unread = mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM messages 
 WHERE receiver_id='$uid' AND is_read=0"));

// Total complaints
$total = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints"));
$pending = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints WHERE status='Pending'"));
$resolved = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints WHERE status='Resolved'"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BCRP4ZV204"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'G-BCRP4ZV204');
</script>
<title>Student Dashboard</title>
<link rel="stylesheet" href="style.css">

<style>
/* Header button layout */
.header-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Chat Button */
.chat-btn {
    padding: 6px 16px;
    background: #6a1b9a;
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-size: 14px;
    font-weight: bold;
    transition: 0.3s;
    position: relative;
}

.chat-btn:hover {
    background: #4a148c;
}

/* 🔴 BADGE */
.badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: red;
    color: white;
    border-radius: 50%;
    padding: 3px 7px;
    font-size: 11px;
}
</style>

</head>

<body style="
background-image: url('https://png.pngtree.com/background/20211215/original/pngtree-graduation-season-student-blue-atmosphere-background-picture-image_1477325.jpg');
background-repeat: no-repeat;
background-size: cover;
background-position: center;
background-attachment: fixed;
min-height: 100vh;
margin: 0;
">

<div class="header">
    <h2>Student Dashboard</h2>

    <!-- ✅ RIGHT SIDE BUTTONS -->
    <div class="header-buttons">

        <!-- Chat Button -->
        <a href="student-chat.php?read=1" class="chat-btn">
            💬 Chat

            <?php if($unread > 0){ ?>
                <span class="badge"><?php echo $unread; ?></span>
            <?php } ?>

        </a>

        <!-- Logout -->
        <a href="logout.php" class="back-btn">
            Logout
        </a>

    </div>
</div>

<div class="container">

    <div class="card blue">
        <h3><?php echo $total; ?></h3>
        <p>Total Complaints</p>
    </div>

    <div class="card orange">
        <h3><?php echo $pending; ?></h3>
        <p>Pending</p>
    </div>

    <div class="card green">
        <h3><?php echo $resolved; ?></h3>
        <p>Resolved</p>
    </div>

</div>

<div class="box">
    <h3>Actions</h3>

    <a href="submit-grievance.php">
        <button>Submit New Grievance</button>
    </a>

    <a href="track-complaint.php">
        <button style="background:#00897b;">
            Track Complaint 
        </button>
    </a>

</div>

</body>
</html>