<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = null;

if (isset($_POST['track'])) {

    $cid = $_POST['complaint_id'];

    // Allow only own complaints (security)
    $uid = $_SESSION['user_id'];

    $result = mysqli_query($conn,
    "SELECT * FROM complaints 
     WHERE id='$cid' AND user_id='$uid'");
}
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
<title>Track Complaint</title>
<link rel="stylesheet" href="style.css">
</head>

<body style="
background-image: url('https://static.vecteezy.com/system/resources/thumbnails/016/412/989/small/abstract-red-background-with-light-blue-gradient-lines-comic-design-vector.jpg');
background-size: cover;
background-position: center;
background-repeat: no-repeat;
background-attachment: fixed;
min-height: 100vh;
margin: 0;
font-family: Arial, sans-serif;
">

<!-- Header -->
<div style="
background:#1a237e;
color:white;
padding:15px 30px;
display:flex;
justify-content:space-between;
align-items:center;
">

    <h2 style="margin:0;">Grievance Tracking System</h2>

    <a href="student-dashboard.php" class="back-btn">
    ⬅ Back
</a>

</div>

<!-- Main Container -->
<div style="
max-width:650px;
margin:30px auto;
padding:20px 25px;

background-image: 
linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
url('https://images.template.net/wp-content/uploads/2016/06/Blue-Backgrounds.jpg');

background-size: cover;
background-position: center;
background-repeat: no-repeat;

border-radius:8px;
box-shadow:0 0 12px rgba(0,0,0,0.25);
">

<h3 style="margin-top:0;color:#1a237e;">Track Your Complaint</h3>

<hr>

<!-- Search Form -->
<form method="POST" style="margin-bottom:25px;">

    <label style="font-weight:bold;">Complaint ID</label>

    <input type="text"
       name="complaint_id"
       placeholder="Enter your Complaint ID"
       required
       inputmode="numeric"
       pattern="[0-9]+"
       style="
       width:100%;
       box-sizing:border-box;
       padding:10px;
       margin-bottom:15px;
       border:1px solid #ccc;
       border-radius:4px;
       ">

<button type="submit"
        name="track"
        style="
        width:100%;
        box-sizing:border-box;
        background:#1a237e;
        color:white;
        border:none;
        padding:12px;
        border-radius:4px;
        cursor:pointer;
        font-weight:bold;
        ">
    Track Complaint
</button>

</form>

<?php if($result){ ?>

<?php if(mysqli_num_rows($result) > 0){

$row = mysqli_fetch_assoc($result);
?>

<!-- Result Table -->
<h4 style="color:#1a237e;">Complaint Details</h4>

<table border="1" width="100%" cellpadding="10"
       style="
       border-collapse:collapse;
       margin-top:10px;
       ">

<tr style="background:#e8eaf6;">
    <th align="left">Complaint ID</th>
    <td><?php echo $row['id']; ?></td>
</tr>

<tr>
    <th align="left">Category</th>
    <td><?php echo $row['category']; ?></td>
</tr>

<tr style="background:#f9f9f9;">
    <th align="left">Subject</th>
    <td><?php echo $row['subject']; ?></td>
</tr>

<tr>
    <th align="left">Description</th>
    <td><?php echo $row['description']; ?></td>
</tr>

<tr style="background:#f9f9f9;">
    <th align="left">Status</th>
    <td style="
        font-weight:bold;
        color:<?php echo ($row['status']=='Resolved')?'green':'orange'; ?>
    ">
        <?php echo $row['status']; ?>
    </td>
</tr>

</table>

<?php } else { ?>

<p style="color:red;font-weight:bold;">
❌ No complaint found with this ID.
</p>

<?php } ?>

<?php } ?>

</div>

</body>
</html>