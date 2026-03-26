<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Sanitize all inputs
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $fav_aircraft = mysqli_real_escape_string($conn, $_POST['favorite_aircraft']);
    $insta = mysqli_real_escape_string($conn, $_POST['instagram']);
    $fb = mysqli_real_escape_string($conn, $_POST['facebook']);
    $dob = $_POST['dob']; // Date format YYYY-MM-DD
    $prof = mysqli_real_escape_string($conn, $_POST['profession']);
    
    // New WhatsApp Field
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);

    $sql = "UPDATE users SET 
            bio = '$bio', 
            location = '$location', 
            favorite_aircraft = '$fav_aircraft', 
            instagram_handle = '$insta',
            facebook_handle = '$fb',
            dob = '$dob',
            profession = '$prof',
            whatsapp_number = '$whatsapp'
            WHERE id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: user_profile.php?msg=updated");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>