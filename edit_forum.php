<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: community.php"); exit();
}

$uid = $_SESSION['user_id'];
$id = mysqli_real_escape_string($conn, $_GET['id']);
$type = $_GET['type'];
$table = ($type == 'post') ? 'forum_posts' : 'forum_comments';

// Fetch Content & Verify Ownership
$sql = "SELECT * FROM $table WHERE id='$id' AND user_id='$uid'";
$res = $conn->query($sql);

if($res->num_rows == 0) { die("Permission denied or content not found."); }
$data = $res->fetch_assoc();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $conn->query("UPDATE $table SET content='$content' WHERE id='$id'");
    header("Location: community.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit <?php echo ucfirst($type); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-box { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        textarea { width: 100%; border: 1px solid #ddd; padding: 10px; border-radius: 5px; font-family: inherit; font-size: 1rem; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="edit-box">
            <h2>Edit <?php echo ucfirst($type); ?></h2>
            <form method="POST">
                <textarea name="content" rows="6" required><?php echo $data['content']; ?></textarea>
                <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                    <a href="community.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>