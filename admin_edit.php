<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if(!$type || !$id) die("Invalid Request");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Content - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f4f7fa; padding: 40px; }
        .edit-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit <?php echo ucfirst($type); ?></h2>
    
    <?php if($type == 'news'): 
        $row = $conn->query("SELECT * FROM latest_news WHERE id='$id'")->fetch_assoc();
    ?>
    <form action="admin_process.php" method="POST">
        <input type="hidden" name="action" value="update_news">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <label>Headline</label>
        <input type="text" name="headline" value="<?php echo $row['headline']; ?>" required>
        
        <label>Content</label>
        <textarea name="content" rows="6" required><?php echo $row['content']; ?></textarea>
        
        <label>Date</label>
        <input type="date" name="date" value="<?php echo $row['published_date']; ?>" required>
        
        <button type="submit" class="btn btn-primary">Update News</button>
    </form>
    <?php endif; ?>

    <?php if($type == 'event'): 
        $row = $conn->query("SELECT * FROM aviation_programs WHERE id='$id'")->fetch_assoc();
    ?>
    <form action="admin_process.php" method="POST">
        <input type="hidden" name="action" value="update_event">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <label>Title</label>
        <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
        
        <label>Description</label>
        <textarea name="description" rows="4" required><?php echo $row['description']; ?></textarea>
        
        <label>Date & Time</label>
        <input type="date" name="date" value="<?php echo $row['event_date']; ?>" required>
        <input type="time" name="time" value="<?php echo $row['event_time']; ?>" required>
        
        <label>Venue</label>
        <input type="text" name="venue" value="<?php echo $row['venue']; ?>" required>
        
        <button type="submit" class="btn btn-primary">Update Event</button>
    </form>
    <?php endif; ?>

    <?php if($type == 'college'): 
        $row = $conn->query("SELECT * FROM aviation_colleges WHERE id='$id'")->fetch_assoc();
    ?>
    <form action="admin_process.php" method="POST">
        <input type="hidden" name="action" value="update_college">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <label>College Name</label>
        <input type="text" name="college_name" value="<?php echo $row['college_name']; ?>" required>
        
        <label>Description</label>
        <textarea name="description" rows="4" required><?php echo $row['description']; ?></textarea>
        
        <label>Contact Number</label>
        <input type="text" name="contact" value="<?php echo $row['contact_number']; ?>">
        
        <label>Website</label>
        <input type="text" name="website" value="<?php echo $row['website_url']; ?>">
        
        <button type="submit" class="btn btn-primary">Update College</button>
    </form>
    <?php endif; ?>
    
    <br>
    <a href="admin_dashboard.php">Cancel</a>
</div>

</body>
</html>