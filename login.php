<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - One World Aviators Club</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
    /* Full Page Background with Dark Overlay */
    body {
        /* Aviation Image URL */
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://www.itaerea.com/wp-content/uploads/air-freedoms.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
        
        /* Centering the Login Card */
        display: flex;
        flex-direction: column;
        /* Note: If your header.php interferes with flexbox on body, remove the display:flex lines */
    }

    /* Make the Login Card Pop */
    .form-container, .card, .login-box { 
        /* Use whatever class wraps your form */
        background: rgba(255, 255, 255, 0.95) !important; /* Slight transparency */
        box-shadow: 0 15px 25px rgba(0,0,0,0.5);
        border-radius: 10px;
        margin: auto; /* Centers it vertically if body is flex */
        max-width: 400px; /* Keeps it neat */
        width: 90%;
        padding: 40px;
    }

    /* White text for the page title if it's outside the card */
    .page-header h1, .page-header p {
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.7);
    }
</style>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Login to access exclusive features</p>
                
                <?php
                if(isset($_GET['msg']) && $_GET['msg']=='registered') {
                    echo '<p style="color:green; font-weight:bold;">Account created! Please login.</p>';
                }
                ?>
            </div>
            
            <form action="auth_login.php" method="POST">
                <div class="form-group">
                    <label>Username or Email</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username_email" required placeholder="Enter username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" required placeholder="Enter password">
                    </div>
                    
                    <div style="text-align: right; margin-top: 5px;">
                        <a href="forgot_password.php" style="font-size: 0.85rem; color: var(--secondary-color); text-decoration: none;">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>

</body>
</html>