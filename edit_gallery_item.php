<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$photo_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check ownership
$sql = "SELECT * FROM plane_spotters_gallery WHERE id = '$photo_id' AND user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) { die("Access Denied or Photo Not Found."); }
$photo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Photo - OWA</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-card { background: white; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .current-img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="edit-card">
            <h2 style="text-align:center; margin-bottom:20px; color:var(--primary-color);">Edit Photo Details</h2>
            <img src="<?php echo $photo['image_path']; ?>" class="current-img">
            
            <form action="gallery_action.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                
                <div class="row">
                    <div class="col form-group">
                        <label>Aircraft Model</label>
                        <input type="text" name="model" class="form-control" value="<?php echo $photo['aircraft_model']; ?>" required>
                    </div>
                    <div class="col form-group">
                        <label>Registration No</label>
                        <input type="text" name="reg_no" class="form-control" value="<?php echo $photo['registration_number']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Airline / Operator</label>
                    <input type="text" name="airline" class="form-control" value="<?php echo $photo['airline_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Flight Route</label>
                    <input type="text" name="route" class="form-control" value="<?php echo $photo['flight_route']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Caption</label>
                    <input type="text" name="caption" class="form-control" value="<?php echo $photo['caption']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Captured Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo $photo['captured_date']; ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width:100%;">Update Details</button>
            </form>
            <br>
            <a href="user_profile.php" style="display:block; text-align:center;">Cancel</a>
        </div>
    </div>
</body>
</html>