<?php
session_start();
include "db.php";

/* Check Login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = "";

/* Submit Complaint */
if (isset($_POST['submit'])) {

    $uid = $_SESSION['user_id'];
    $category = $_POST['category'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // OPTIONAL FILE UPLOAD
    $file_path = "";

    if(isset($_FILES['proof']) && $_FILES['proof']['name'] != ""){

        $file_name = $_FILES['proof']['name'];
        $tmp_name = $_FILES['proof']['tmp_name'];

        if (!is_dir("uploads")) {
            mkdir("uploads");
        }

        $file_path = "uploads/" . time() . "_" . $file_name;
        move_uploaded_file($tmp_name, $file_path);
    }

    // INSERT INTO DATABASE
    mysqli_query($conn,
    "INSERT INTO complaints
    (user_id, category, subject, description, proof, status)
    VALUES
    ('$uid','$category','$subject','$description','$file_path','Pending')");

    $complaint_id = mysqli_insert_id($conn);
    $success_message = "Grievance Submitted Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Grievance</title>
<link rel="stylesheet" href="style.css">

<!-- Google Analytics GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BCRP4ZV204"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-BCRP4ZV204');
</script>

<style>
.new-form-container {
    width: 420px;
    max-width: 90%;
    min-height: 480px;
    margin: 0 auto;
    position: relative;
    top: 20px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    border-radius: 15px;
    padding: 30px 25px 40px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    color: white;
}
</style>

</head>

<body style="
background-image: url('https://images.openai.com/static-rsc-3/8TzB0pi_o4_nlt6YYLtjx-5aXqPBySw4oWHyj59BJewsETAO2UhZUa42wqBt29kk1mdXm-wJQLKLUIyWFEliHobtPSL80r3VnBWRM6Zrtr4?purpose=fullsize&v=1');
background-repeat: no-repeat;
background-size: cover;
background-position: center;
min-height: 100vh;
margin: 0;
">

<!-- Header -->
<div class="header">
    <h2>Submit Grievance</h2>
    <a href="student-dashboard.php" class="back-btn">
        ⬅ Back
    </a>
</div>

<marquee behavior="alternate" direction="right"
style="background:#207cca; color:white;
padding:10px; font-size:18px; font-weight:bold;">
False or malicious complaints may lead to disciplinary action.
</marquee>

<!-- Form Box -->
<div class="new-form-container">

    <h2>Submit Your Grievance</h2>
    <p class="sub-text">We are here to help you. Please fill the form carefully.</p>

    <form method="POST"
          enctype="multipart/form-data"
          onsubmit="gtag('event','grievance_submitted');">

        <label>Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option>Hostel</option>
            <option>Examination</option>
            <option>Library</option>
            <option>Transport</option>
        </select>

        <label>Subject</label>
        <input type="text"
               name="subject"
               placeholder="Enter subject"
               required>

        <label>Description</label>
        <textarea name="description"
                  rows="4"
                  placeholder="Describe your issue..."
                  required></textarea>

        <label>Upload Evidence (Optional)</label>
        <input type="file" name="proof">

        <button type="submit" name="submit">
            Submit Grievance
        </button>

    </form>

</div>

<!-- SUCCESS POPUP -->
<?php if($success_message != "") { ?>

<div class="success-modal">
    <div class="success-box">
        <h2>✅ Submitted Successfully!</h2>
        <p>Your grievance has been recorded.</p>
        <p><b>Complaint ID:</b> <?php echo $complaint_id; ?></p>
        <button onclick="closeSuccess()">OK</button>
    </div>
</div>

<script>
gtag('event', 'grievance_submitted_success');

function closeSuccess() {
    document.querySelector('.success-modal').style.display = 'none';
}
</script>

<?php } ?>

</body>
</html>