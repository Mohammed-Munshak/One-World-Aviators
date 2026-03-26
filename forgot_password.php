<?php
session_start();
require 'db_connect.php';

$msg = "";
$msg_color = "red";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $new_pass = $_POST['new_password'];

    // 1. Verify User Exists with matching WhatsApp
    $sql = "SELECT id FROM users WHERE username='$username' AND whatsapp_number='$whatsapp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $uid = $user['id'];

        // 2. Check if request already exists
        $check = $conn->query("SELECT id FROM password_requests WHERE user_id='$uid'");
        if ($check->num_rows > 0) {
            $msg = "You already have a pending request. Please wait for Admin approval.";
        } else {
            // 3. Save Request (Plain text temporarily so Admin can approve exactly this)
            // Note: In production, storing plain text is risky, but necessary for this specific manual workflow.
            $stmt = $conn->prepare("INSERT INTO password_requests (user_id, requested_password) VALUES (?, ?)");
            $stmt->bind_param("is", $uid, $new_pass);
            
            if ($stmt->execute()) {
                // 4. Notify Admin
                $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) 
                              SELECT id, '$uid', 'pass_request', 0 FROM users WHERE role='admin'");
                
                $msg = "Request sent! An Admin will verify your WhatsApp and approve the change shortly.";
                $msg_color = "green";
            } else {
                $msg = "Error submitting request.";
            }
        }
    } else {
        $msg = "Username and WhatsApp number do not match our records.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Password Reset</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Password Strength Styles */
        .strength-meter { height: 5px; background: #ddd; margin-top: 5px; border-radius: 3px; overflow: hidden; display: flex; }
        .strength-bar { height: 100%; width: 0; transition: width 0.3s, background 0.3s; }
        .strength-text { font-size: 0.75rem; margin-top: 3px; font-weight: bold; float: right;}
    </style>
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-box">
            <h2>Reset Password</h2>
            <p>Enter your details to request a new password. <b>Admin will contact you soon !!</b></p>
            
            <?php if($msg): ?>
                <div style="background: <?php echo $msg_color=='green'?'#d4edda':'#f8d7da'; ?>; color: <?php echo $msg_color=='green'?'#155724':'#721c24'; ?>; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Your Username">
                </div>
                
                <div class="form-group">
                    <label>WhatsApp Number</label>
                    <input type="text" name="whatsapp" required placeholder="Registered WhatsApp Number">
                </div>

                <div class="form-group">
                    <label>New Requested Password</label>
                    <input type="password" name="new_password" id="password" required onkeyup="checkStrength()" placeholder="Enter new strong password">
                    <div class="strength-meter">
                        <div class="strength-bar" id="strength-bar"></div>
                    </div>
                    <div class="strength-text" id="strength-text"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
            </form>
            
            <div class="auth-footer">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>

    <script>
    function checkStrength() {
        const val = document.getElementById('password').value;
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        let score = 0;

        // Simple Strength Logic
        if (val.length > 5) score++;
        if (val.length > 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        let color = "#ddd";
        let width = "0%";
        let status = "";

        if(score < 2) { color = "#dc3545"; width = "30%"; status = "Weak"; }
        else if(score < 4) { color = "#ffc107"; width = "60%"; status = "Medium"; }
        else { color = "#28a745"; width = "100%"; status = "Strong"; }

        if(val.length === 0) { width = "0%"; status = ""; }

        bar.style.backgroundColor = color;
        bar.style.width = width;
        text.style.color = color;
        text.innerText = status;
    }
    </script>
</body>
</html>