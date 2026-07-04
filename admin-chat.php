<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role']!="admin") {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

if(isset($_GET['student_id'])){
    $student_id = intval($_GET['student_id']);
} else {
    echo "No student selected";
    exit();
}

/* Get student name */
$user = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT username FROM users WHERE id='$student_id'"));
$student_name = $user['username'];

/* SEND MESSAGE */
if(isset($_POST['send'])){
    $msg = $_POST['message'];
    $file_path = "";

    if(isset($_FILES['file']) && $_FILES['file']['name'] != ""){
        if (!is_dir("uploads")) mkdir("uploads");

        $file_path = "uploads/" . time() . "_" . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
    }

    mysqli_query($conn,
    "INSERT INTO messages (sender_id, receiver_id, message, file, is_read)
     VALUES ('$admin_id','$student_id','$msg','$file_path',0)");
}

/* FETCH CHAT */
$chat = mysqli_query($conn,
"SELECT * FROM messages 
 WHERE (sender_id='$student_id' AND receiver_id='$admin_id')
    OR (sender_id='$admin_id' AND receiver_id='$student_id')
 ORDER BY id ASC");

/* ✅ MARK ONLY UNREAD STUDENT MESSAGES AS READ */
mysqli_query($conn,
"UPDATE messages 
 SET is_read = 1 
 WHERE receiver_id = '$admin_id' 
 AND sender_id = '$student_id'
 AND is_read = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Chat</title>
<link rel="stylesheet" href="style.css">

<style>

/* CONTAINER */
.chat-container {
    max-width: 700px;
    margin: 20px auto;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 15px;
}

/* CHAT BOX */
.chat-box {
    height: 350px;
    overflow-y: auto;
    padding: 10px;
    border-radius: 10px;
    background: rgba(255,255,255,0.9);
    display: flex;
    flex-direction: column;
}

/* MESSAGE ROW */
.msg-row {
    display: flex;
    margin: 6px 0;
}
.msg-row.me { justify-content: flex-end; }
.msg-row.other { justify-content: flex-start; }

/* MESSAGE */
.msg {
    padding: 10px 14px;
    max-width: 60%;
    border-radius: 15px;
    font-size: 14px;
}

/* ADMIN MESSAGE */
.msg-me {
    background: #0d47a1;
    color: white;
}

/* STUDENT MESSAGE */
.msg-other {
    background: #f1f1f1;
    color: black;
}

/* INPUT AREA */
.chat-input {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 15px;
    width: 100%;
}

/* TEXT INPUT */
.chat-input input[type="text"] {
    flex: 1;
    height: 45px;
    min-width: 200px;
    padding: 0 15px;
    border-radius: 25px;
    border: 1px solid #ccc;
    background: white;
    outline: none;
}

/* FILE INPUT */
.chat-input input[type="file"] {
    height: 45px;
    border-radius: 25px;
    border: 1px solid #ccc;
    padding: 5px 10px;
    background: white;
}

/* SEND BUTTON */
.chat-input button {
    height: 45px;
    padding: 0 18px;
    border-radius: 25px;
    background: #0d47a1;
    color: white;
    border: none;
    cursor: pointer;
}

.chat-input button:hover {
    background: #08306b;
}

</style>

</head>

<body>

<div class="header">
    <h2>Chat with <?php echo $student_name; ?></h2>
    <a href="admin-dashboard.php" class="back-btn">⬅ Back</a>
</div>

<div class="chat-container">

<div class="chat-box" id="chatBox">

<?php while($row = mysqli_fetch_assoc($chat)){ ?>

<div class="msg-row <?php echo ($row['sender_id'] == $admin_id) ? 'me' : 'other'; ?>">

    <div class="msg <?php echo ($row['sender_id'] == $admin_id) ? 'msg-me' : 'msg-other'; ?>">
        <?php echo $row['message']; ?>

        <?php if(!empty($row['file'])){ ?>
            <br>
            <a href="<?php echo $row['file']; ?>" target="_blank" style="font-size:12px;color:#fff;">
                📎 View File
            </a>
        <?php } ?>
    </div>

</div>

<?php } ?>

</div>

<form method="POST" enctype="multipart/form-data" class="chat-input">

    <input type="file" name="file">

    <input type="text" name="message" placeholder="Type message...">

    <button name="send">Send</button>

</form>

</div>

<script>
let chatBox = document.getElementById("chatBox");
chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>