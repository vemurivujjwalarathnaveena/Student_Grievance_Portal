<?php
session_start();
include "db.php";

/* SECURITY */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';
$admin_dept = $_SESSION['department'] ?? '';

/* 🔧 DEBUG (REMOVE AFTER TEST) */
// echo "Role: $role | Dept: $admin_dept"; exit();

/* RESOLVE */
if (isset($_GET['resolve'])) {
    $id = $_GET['resolve'];
    mysqli_query($conn,
    "UPDATE complaints SET status='Resolved' WHERE id='$id'");
}

/* 🔥 ROLE LOGIC */
if($role == "superadmin"){

    $total = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints"));
    $pending = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints WHERE status='Pending'"));
    $resolved = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints WHERE status='Resolved'"));

    $data = mysqli_query($conn,"SELECT * FROM complaints ORDER BY id DESC");

} else {

    /* ❌ if department missing */
    if(empty($admin_dept)){
        echo "<h2 style='color:red;text-align:center;'>No department assigned!</h2>";
        exit();
    }

    /* ✅ FILTER BY CATEGORY */
    $total = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM complaints WHERE category='$admin_dept'"));

    $pending = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM complaints WHERE status='Pending' AND category='$admin_dept'"));

    $resolved = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM complaints WHERE status='Resolved' AND category='$admin_dept'"));

    $data = mysqli_query($conn,
    "SELECT * FROM complaints 
     WHERE category='$admin_dept'
     ORDER BY id DESC");
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
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">

<style>
.complaint{
    background: rgba(255,255,255,0.85);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.resolve-btn{
    background: green;
    color: white;
    border: none;
    padding: 6px 15px;
    border-radius: 4px;
    cursor: pointer;
}

.proof-btn{
    background: #1565c0;
    color: white;
    border: none;
    padding: 6px 15px;   /* same as resolve */
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    width: auto;          /* ❌ REMOVE FULL WIDTH */
    display: inline-block;/* ✅ KEEP INLINE */
    margin-right: 5px;    /* spacing between buttons */
}

.chat-btn{
    background:#6a1b9a;
    color:white;
    border:none;
    padding:6px 15px;
    border-radius:4px;
    cursor:pointer;
}

.badge{
    position:absolute;
    top:-6px;
    right:-6px;
    background:red;
    color:white;
    border-radius:50%;
    padding:3px 7px;
    font-size:11px;
}
</style>
</head>

<body>

<div class="header">
    <h2>
        <?php 
        if($role == "superadmin"){
            echo "Main Admin Dashboard";
        } else {
            echo $admin_dept . " Department Dashboard";
        }
        ?>
    </h2>

    <div class="header-buttons">
        <a href="admin-progress.php">
            <button class="progress-btn">📊 View Progress</button>
        </a>
        <a href="#" onclick="printComplaints()" class="print-btn">🖨 Print</a>
        <a href="logout.php" class="back-btn">Logout</a>
    </div>
</div>

<div class="container">

    <div class="card blue" onclick="filterComplaints('All')">
        <h3><?php echo $total; ?></h3>
        <p>Total Complaints</p>
    </div>

    <div class="card orange" onclick="filterComplaints('Pending')">
        <h3><?php echo $pending; ?></h3>
        <p>Pending</p>
    </div>

    <div class="card green" onclick="filterComplaints('Resolved')">
        <h3><?php echo $resolved; ?></h3>
        <p>Resolved</p>
    </div>

</div>

<div class="box" id="print-section">

<h3>Complaints</h3>

<?php while($row = mysqli_fetch_assoc($data)){ 

    $student_id = $row['user_id'];

    $unread = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM messages 
     WHERE receiver_id = '$admin_id' 
     AND sender_id = '$student_id' 
     AND is_read = 0"));
?>

<div class="complaint" data-status="<?php echo $row['status']; ?>">

    <p><b>Category:</b> <?php echo $row['category']; ?></p>
    <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
    <p><b>Description:</b> <?php echo $row['description']; ?></p>

    <p><b>Evidence:</b>
        <?php if(!empty($row['proof'])){ ?>
            <a href="<?php echo $row['proof']; ?>" target="_blank">
                <button class="proof-btn">View</button>
            </a>
            <a href="<?php echo $row['proof']; ?>" download>
                <button class="proof-btn">Download</button>
            </a>
        <?php } else { ?>
            No evidence uploaded
        <?php } ?>
    </p>

    <p><b>Status:</b> <?php echo $row['status']; ?></p>

    <?php if($row['status']=="Pending"){ ?>
        <a href="admin-dashboard.php?resolve=<?php echo $row['id']; ?>">
            <button class="resolve-btn">Resolve</button>
        </a>
    <?php } ?>

    <div style="display:inline-block; position:relative; margin-left:10px;">
        <a href="admin-chat.php?student_id=<?php echo $student_id; ?>">
            <button class="chat-btn">Chat</button>
        </a>

        <?php if($unread > 0){ ?>
            <span class="badge"><?php echo $unread; ?></span>
        <?php } ?>
    </div>

</div>

<?php } ?>

</div>

<script>
function filterComplaints(status){
    let complaints = document.querySelectorAll(".complaint");

    complaints.forEach(function(item){
        let itemStatus = item.getAttribute("data-status");

        if(status === "All"){
            item.style.display = "block";
        }
        else if(itemStatus === status){
            item.style.display = "block";
        }
        else{
            item.style.display = "none";
        }
    });
}

function printComplaints() {
    window.print();
}
</script>

</body>
</html>