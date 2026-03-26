<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $date = $_POST['date'];
    $airline = mysqli_real_escape_string($conn, $_POST['airline']);
    $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);
    $route = mysqli_real_escape_string($conn, $_POST['route']);
    
    $user_id = $_SESSION['user_id'];
    $target_dir = "images/gallery/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $filename = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) { $message = "File is not an image."; $uploadOk = 0; }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $message = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            
            $sql = "INSERT INTO plane_spotters_gallery (user_id, aircraft_model, caption, captured_date, image_path, status, airline_name, registration_number, flight_route) 
                    VALUES ('$user_id', '$model', '$caption', '$date', '$target_file', 'approved', '$airline', '$reg_no', '$route')";
            
            if ($conn->query($sql) === TRUE) {
                
                // --- FIX: GET THE PHOTO ID ---
                $new_photo_id = $conn->insert_id;
                
                // Notify Admins (Only if I am not the admin)
                $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
                while($admin = $admins->fetch_assoc()) {
                    $aid = $admin['id'];
                    if($aid != $user_id) {
                        // STORE THE PHOTO ID IN REFERENCE_ID
                        $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$aid', '$user_id', 'upload', '$new_photo_id')");
                    }
                }

                header("Location: gallery.php");
                exit();
            } else {
                $message = "Database Error: " . $conn->error;
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Photo - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .upload-card { background: white; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }
        .error-msg { color: red; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="content-section">
        <div class="container">
            <div class="upload-card">
                <h2 style="text-align:center; margin-bottom:20px; color:var(--primary-color);">Upload to Gallery</h2>
                <?php if($message): ?>
                    <div class="error-msg"><?php echo $message; ?></div>
                <?php endif; ?>

                <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col form-group">
                            <label>Aircraft Model</label>
                            <input type="text" name="model" class="form-control" required placeholder="e.g. Airbus A330">
                        </div>
                        <div class="col form-group">
                            <label>Registration No</label>
                            <input type="text" name="reg_no" class="form-control" required placeholder="e.g. 4R-ALM">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Airline / Operator</label>
                        <input type="text" name="airline" class="form-control" required placeholder="e.g. SriLankan Airlines">
                    </div>

                    <div class="form-group">
                        <label>Flight Route (Optional)</label>
                        <input type="text" name="route" class="form-control" placeholder="e.g. CMB to LHR">
                    </div>

                    <div class="form-group">
                        <label>Caption / Location</label>
                        <input type="text" name="caption" class="form-control" required placeholder="e.g. Landing at BIA">
                    </div>

                    <div class="form-group">
                        <label>Date Captured</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Select Photo</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">Upload Photo</button>
                    <br><br>
                    <a href="gallery.php" style="display:block; text-align:center;">Cancel</a>
                </form>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>