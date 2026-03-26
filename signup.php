<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Password Strength Styles */
        .strength-meter { height: 5px; background: #ddd; margin-top: 5px; border-radius: 3px; overflow: hidden; display: flex; }
        .strength-bar { height: 100%; width: 0; transition: width 0.3s, background 0.3s; }
        .strength-text { font-size: 0.75rem; margin-top: 3px; font-weight: bold; }
    </style>
</head>
<style>
    body {
        /* Different Aviation Image for Signup (Cockpit View) */
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://www.itaerea.com/wp-content/uploads/air-freedoms.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* Style the form container */
    .form-container, .signup-box {
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        border-radius: 10px;
        margin: 50px auto; /* Margin top/bottom for scrolling */
        max-width: 500px;
        padding: 40px;
    }
    
    /* Ensure footer stays at bottom or looks good */
    footer {
        background: rgba(0,0,0,0.8);
        color: white;
        margin-top: auto;
    }
</style>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-box">
            <h2>Join the Club</h2>
            <p>Start your aviation journey today</p>
            
            <form action="auth_signup.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>WhatsApp Number (For Verification)</label>
                    <input type="text" name="whatsapp" required>
                    <small style="color:#666; font-size:0.75rem;">Admins will contact you on this number.</small>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" required onkeyup="checkStrength()">
                    <div class="strength-meter">
                        <div class="strength-bar" id="strength-bar"></div>
                    </div>
                    <div class="strength-text" id="strength-text"></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
            </form>
            <div class="auth-footer">
                <p>Already a member? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <script>
    function checkStrength() {
        const val = document.getElementById('password').value;
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        let score = 0;

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