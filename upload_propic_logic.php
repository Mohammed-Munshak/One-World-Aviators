<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_image'];

    // File properties
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Work out the file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($file_ext, $allowed)) {
        if ($file_error === 0) {
            if ($file_size < 5000000) { // Limit to 5MB
                
                // Create unique name to prevent overwriting (e.g., profile_25.jpg)
                $new_file_name = "profile_" . $user_id . "." . $file_ext;
                $file_destination = 'images/users/' . $new_file_name;

                // Ensure directory exists
                if (!is_dir('images/users/')) {
                    mkdir('images/users/', 0777, true);
                }

                // Move file from temporary storage to our folder
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    
                    // Update Database
                    $sql = "UPDATE users SET profile_pic = '$file_destination' WHERE id = '$user_id'";
                    
                    if ($conn->query($sql) === TRUE) {
                        header("Location: user_profile.php?upload=success");
                    } else {
                        echo "Database Error.";
                    }

                } else {
                    echo "Failed to move file.";
                }

            } else {
                echo "File is too big! Max 5MB.";
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "You can only upload jpg, jpeg, or png files!";
    }
}
?>