<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || 
   ($_SESSION['role'] != "admin" && $_SESSION['role'] != "superadmin")) {
    header("Location: login.php");
    exit();
}

/* FILTERS */
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

$where = "1";

/* 🔥 DEPARTMENT FILTER */
if($_SESSION['role'] != "superadmin"){
    $dept = $_SESSION['department'];
    $where .= " AND category='$dept'";
}

/* DATE RANGE */
if($from_date != '' && $to_date != ''){
    $where .= " AND DATE(created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif($from_date != ''){
    $where .= " AND DATE(created_at) >= '$from_date'";
} elseif($to_date != ''){
    $where .= " AND DATE(created_at) <= '$to_date'";
}

if($category != ''){
    $where .= " AND category = '$category'";
}

if($status != ''){
    $where .= " AND status = '$status'";
}

/* CATEGORY DATA */
$cat_q = mysqli_query($conn,"SELECT category, COUNT(*) total FROM complaints WHERE $where GROUP BY category");
$cat_labels=[]; $cat_values=[];
while($r=mysqli_fetch_assoc($cat_q)){
    $cat_labels[]=$r['category'];
    $cat_values[]=$r['total'];
}

/* STATUS DATA */
$st_q = mysqli_query($conn,"SELECT status, COUNT(*) total FROM complaints WHERE $where GROUP BY status");
$st_labels=[]; $st_values=[];
while($r=mysqli_fetch_assoc($st_q)){
    $st_labels[]=$r['status'];
    $st_values[]=$r['total'];
}

/* TREND DATA */
$trend_q = mysqli_query($conn,
"SELECT DATE(created_at) d, COUNT(*) total 
 FROM complaints 
 WHERE $where
 GROUP BY d ORDER BY d ASC");

$trend_labels=[]; $trend_values=[];
while($r=mysqli_fetch_assoc($trend_q)){
    $trend_labels[]=$r['d'];
    $trend_values[]=$r['total'];
}

/* DROPDOWNS */
$cat_list = mysqli_query($conn,"SELECT DISTINCT category FROM complaints");
$st_list = mysqli_query($conn,"SELECT DISTINCT status FROM complaints");
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Analytics</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body { margin:0; font-family: Arial; }

.header {
    display:flex;
    justify-content:space-between;
    padding:15px;
}

.filters {
    max-width: 850px;
    margin: 20px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-group {
    flex: 1 1 200px;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-size: 13px;
    margin-bottom: 5px;
}

.filters input,
.filters select {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* 🔥 BUTTON FIX */
.filter-actions {
    margin-top: 15px;
    display:flex;
    gap:15px;
    justify-content:center;
    flex-wrap: wrap;
}

.filter-actions button {
    width:140px;
    height:40px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
}

.apply-btn { background:#0d47a1; color:white; }
.reset-btn { background:gray; color:white; }
.print-btn { background:green; color:white; }

.filter-actions button:hover {
    opacity:0.9;
}

/* CHARTS */
.chart-container {
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:25px;
    margin-top:20px;
}

.chart-box {
    width:380px;
    height:300px;
    background:white;
    padding:15px;
    border-radius:10px;
}

.chart-box canvas {
    height:230px !important;
}

.full {
    width:800px;
}

</style>
</head>

<body>

<div class="header">
    <h2>📊 Complaint Analytics</h2>
    <a href="admin-dashboard.php" class="back-btn">⬅ Back</a>
</div>

<div class="filters">
<form method="GET">

<div class="filter-row">

<div class="filter-group">
<label>From Date</label>
<input type="date" name="from_date" value="<?php echo $from_date; ?>">
</div>

<div class="filter-group">
<label>To Date</label>
<input type="date" name="to_date" value="<?php echo $to_date; ?>">
</div>

<div class="filter-group">
<label>Category</label>
<select name="category">
<option value="">All Category</option>
<?php while($c=mysqli_fetch_assoc($cat_list)){ ?>
<option value="<?php echo $c['category']; ?>" <?php if($category==$c['category']) echo "selected"; ?>>
<?php echo $c['category']; ?>
</option>
<?php } ?>
</select>
</div>

<div class="filter-group">
<label>Status</label>
<select name="status">
<option value="">All Status</option>
<?php while($s=mysqli_fetch_assoc($st_list)){ ?>
<option value="<?php echo $s['status']; ?>" <?php if($status==$s['status']) echo "selected"; ?>>
<?php echo $s['status']; ?>
</option>
<?php } ?>
</select>
</div>

</div>

<div class="filter-actions">
<button class="apply-btn">Apply</button>

<button type="button" class="reset-btn" onclick="window.location='admin-progress.php'">
Reset
</button>

<button type="button" class="print-btn" onclick="window.print()">Print</button>
</div>

</form>
</div>

<div class="chart-container">

<div class="chart-box">
<h3>Category</h3>
<canvas id="catChart"></canvas>
</div>

<div class="chart-box">
<h3>Status</h3>
<canvas id="stChart"></canvas>
</div>

<div class="chart-box full">
<h3>Trend</h3>
<canvas id="trendChart"></canvas>
</div>

</div>

<script>

new Chart(document.getElementById("catChart"), {
type:'pie',
data:{labels:<?php echo json_encode($cat_labels); ?>,
datasets:[{data:<?php echo json_encode($cat_values); ?>}]}
});

new Chart(document.getElementById("stChart"), {
type:'bar',
data:{labels:<?php echo json_encode($st_labels); ?>,
datasets:[{data:<?php echo json_encode($st_values); ?>}]}
});

new Chart(document.getElementById("trendChart"), {
type:'line',
data:{labels:<?php echo json_encode($trend_labels); ?>,
datasets:[{label:"Complaints",data:<?php echo json_encode($trend_values); ?>}]}
});

</script>

</body>
</html>