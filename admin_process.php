<?php
session_start();
require 'db_connect.php';

// 1. SECURITY: Only allow Admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// =========================================================
// 1. ADD NEW CONTENT (CREATE)
// =========================================================

// --- ADD NEWS (FIXED DATE & TIMEZONE) ---
if ($action == 'add_news') {
    $headline = mysqli_real_escape_string($conn, $_POST['headline']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    // Set Timezone to Sri Lanka & Get Current Time
    date_default_timezone_set('Asia/Colombo'); 
    $date = date('Y-m-d H:i:s'); 

    // Image Upload Logic
    $target_dir = "images/news/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $filename = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;
    
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Insert into DB
            $sql = "INSERT INTO latest_news (headline, content, published_date, image_path) 
                    VALUES ('$headline', '$content', '$date', '$target_file')";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: admin_dashboard.php?tab=news&msg=success");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }
}

// --- ADD EVENT ---
if ($action == 'add_event' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);
    $note = mysqli_real_escape_string($conn, $_POST['special_note']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    $target_dir = "images/events/";
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_path = $target_file;
    }

    $sql = "INSERT INTO aviation_programs (title, description, venue, event_date, event_time, special_note, image_path) 
            VALUES ('$title', '$desc', '$venue', '$date', '$time', '$note', '$image_path')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php?tab=events&msg=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// --- ADD COLLEGE ---
if ($action == 'add_college' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['college_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $courses = mysqli_real_escape_string($conn, $_POST['courses']);
    $branch1 = mysqli_real_escape_string($conn, $_POST['branch1']);
    $branch2 = mysqli_real_escape_string($conn, $_POST['branch2']);
    $contact1 = $_POST['contact1'];
    $contact2 = $_POST['contact2'];
    $email = $_POST['email'];
    $web = $_POST['website'];
    $addr = mysqli_real_escape_string($conn, $_POST['address']);
    
    $target_dir = "images/colleges/";
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    // 1. Main Image
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_file = $target_dir . time() . "_main_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_path = $target_file;
    }

    // 2. Icon Logo
    $icon_path = "";
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0) {
        $target_file = $target_dir . time() . "_icon_" . basename($_FILES["icon"]["name"]);
        move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file);
        $icon_path = $target_file;
    }

    $sql = "INSERT INTO aviation_colleges 
            (college_name, description, courses_list, branch_1, branch_2, contact_number, contact_number_2, email, website_url, location_address, image_path, icon_path) 
            VALUES ('$name', '$desc', '$courses', '$branch1', '$branch2', '$contact1', '$contact2', '$email', '$web', '$addr', '$image_path', '$icon_path')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php?tab=colleges&msg=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// --- CREATE ADMIN ---
if ($action == 'create_admin' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass_plain = $_POST['password']; 

    // Hash the password
    $pass_hashed = password_hash($pass_plain, PASSWORD_DEFAULT);

    $check = $conn->query("SELECT id FROM users WHERE username='$user' OR email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('User already exists!'); window.location.href='admin_dashboard.php?tab=admins';</script>";
    } else {
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$user', '$email', '$pass_hashed', 'admin')";
        if ($conn->query($sql) === TRUE) {
            header("Location: admin_dashboard.php?tab=admins&msg=created");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// =========================================================
// 2. DELETE CONTENT
// =========================================================

if ($action == 'delete_news' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM latest_news WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=news");
    exit();
}

if ($action == 'delete_event' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM aviation_programs WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=events");
    exit();
}

if ($action == 'delete_college' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM aviation_colleges WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=colleges");
    exit();
}

if ($action == 'delete_comment' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM comments WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=comments");
    exit();
}

if ($action == 'delete_gallery_item' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Optional: Delete the file from server folder too
    $res = $conn->query("SELECT image_path FROM plane_spotters_gallery WHERE id='$id'");
    if($r = $res->fetch_assoc()) {
        if(file_exists($r['image_path'])) unlink($r['image_path']);
    }
    $conn->query("DELETE FROM plane_spotters_gallery WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=gallery");
    exit();
}

if ($action == 'delete_story' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM college_success_stories WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=stories");
    exit();
}

// --- DELETE MESSAGE (NEW) ---
if ($action == 'delete_message' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $conn->query("DELETE FROM contact_messages WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=messages&msg=deleted");
    exit();
}

// =========================================================
// 3. USER MANAGEMENT & UPDATES
// =========================================================

// --- VERIFY USER ---
if ($action == 'verify_user' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE users SET is_verified_member=1 WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=users"); // Changed to 'users' tab
    exit();
}

// --- DELETE USER ---
if ($action == 'delete_user' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM users WHERE id='$id'"); 
    header("Location: admin_dashboard.php?tab=users"); // Changed to 'users' tab
    exit();
}

// --- MAKE ADMIN (NEW) ---
if ($action == 'make_admin' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $conn->query("UPDATE users SET role='admin' WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=users&msg=promoted");
    exit();
}

// --- REMOVE ADMIN (NEW) ---
if ($action == 'remove_admin' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    // Protect against removing yourself
    if($id != $_SESSION['user_id']) {
        $conn->query("UPDATE users SET role='user' WHERE id='$id'");
    }
    header("Location: admin_dashboard.php?tab=users&msg=demoted");
    exit();
}

// --- PASSWORD REQUESTS ---

if ($action == 'approve_pass_reset' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $req_id = $_POST['request_id'];
    $req = $conn->query("SELECT user_id, requested_password FROM password_requests WHERE id='$req_id'")->fetch_assoc();
    
    if ($req) {
        $uid = $req['user_id'];
        $new_pass_plain = $req['requested_password'];
        $new_pass_hashed = password_hash($new_pass_plain, PASSWORD_DEFAULT);
        
        $conn->query("UPDATE users SET password='$new_pass_hashed' WHERE id='$uid'");
        $conn->query("DELETE FROM password_requests WHERE id='$req_id'");
        
        header("Location: admin_dashboard.php?tab=users&msg=pass_updated");
    } else {
        echo "Request not found.";
    }
    exit();
}

if ($action == 'reject_pass_reset' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM password_requests WHERE id='$id'");
    header("Location: admin_dashboard.php?tab=users&msg=pass_rejected");
    exit();
}

// --- MANUAL PASSWORD CHANGE ---
if ($action == 'manual_pass_change' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['user_id'];
    $plain_pass = $_POST['new_pass'];
    $hashed_pass = password_hash($plain_pass, PASSWORD_DEFAULT);
    
    $sql = "UPDATE users SET password='$hashed_pass' WHERE id='$uid'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Password successfully changed!'); window.location.href='admin_dashboard.php?tab=users';</script>";
    } else {
        echo "Error updating password: " . $conn->error;
    }
}

$conn->close();
?>