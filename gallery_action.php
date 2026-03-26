<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

$user_id = $_SESSION['user_id'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// --- DELETE PHOTO ---
if ($action == 'delete' && isset($_GET['id'])) {
    $photo_id = $_GET['id'];
    
    // Verify ownership
    $check = $conn->query("SELECT * FROM plane_spotters_gallery WHERE id='$photo_id' AND user_id='$user_id'");
    
    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $file_path = $row['image_path'];
        
        $conn->query("DELETE FROM plane_spotters_gallery WHERE id='$photo_id'");
        
        if (file_exists($file_path)) { unlink($file_path); }
        
        header("Location: user_profile.php?msg=deleted");
    } else {
        die("Error: You do not own this photo.");
    }
}

// --- UPDATE PHOTO ---
if ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo_id = $_POST['photo_id'];
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $date = $_POST['date'];
    
    // NEW FIELDS
    $airline = mysqli_real_escape_string($conn, $_POST['airline']);
    $reg = mysqli_real_escape_string($conn, $_POST['reg_no']);
    $route = mysqli_real_escape_string($conn, $_POST['route']);

    // Verify ownership
    $check = $conn->query("SELECT id FROM plane_spotters_gallery WHERE id='$photo_id' AND user_id='$user_id'");
    
    if ($check->num_rows > 0) {
        $sql = "UPDATE plane_spotters_gallery SET 
                aircraft_model='$model', 
                caption='$caption', 
                captured_date='$date',
                airline_name='$airline',
                registration_number='$reg',
                flight_route='$route'
                WHERE id='$photo_id'";
                
        if ($conn->query($sql) === TRUE) {
            header("Location: user_profile.php?msg=updated");
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        die("Error: You do not own this photo.");
    }
}
?>