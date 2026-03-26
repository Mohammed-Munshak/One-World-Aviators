<div class="top-bar">
    <div class="container">
        <div class="top-left">
            <a href="https://whatsapp.com/channel/0029VbAy0BC8qIzmNzGxL71K" target="_blank" class="whatsapp-link">
                <i class="fa-brands fa-whatsapp"></i> Join our WhatsApp Channel here
            </a>
        </div>
        
        <div class="top-right">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-profile-header">
                    
                    <?php 
                        // Count Unread Notifications
                        $my_uid = $_SESSION['user_id'];
                        $notif_count = 0;
                        
                        // Check if database connection exists before querying
                        if(isset($conn)) {
                            $notif_sql = "SELECT COUNT(*) FROM notifications WHERE user_id='$my_uid' AND is_read=0";
                            $notif_res = $conn->query($notif_sql);
                            if($notif_res) {
                                $notif_count = $notif_res->fetch_row()[0];
                            }
                        }
                    ?>
                    <a href="notifications.php" class="header-link" style="position:relative; margin-right:15px; font-size:1.1rem; color: #fff;" title="Notifications">
                        <i class="fa-solid fa-bell"></i>
                        <?php if($notif_count > 0): ?>
                            <span style="position:absolute; top:-8px; right:-8px; background:red; color:white; font-size:0.7rem; padding:2px 5px; border-radius:50%; min-width: 15px; text-align: center;">
                                <?php echo $notif_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php 
                        // Logic to get profile pic
                        $prof_pic = 'images/default_avatar.png'; // Default
                        
                        if(isset($conn)) {
                            $uid = $_SESSION['user_id'];
                            $u_sql = "SELECT profile_pic FROM users WHERE id = '$uid'";
                            $u_res = $conn->query($u_sql);
                            if($u_res && $u_row = $u_res->fetch_assoc()) {
                                if(!empty($u_row['profile_pic'])) {
                                    $prof_pic = $u_row['profile_pic'];
                                }
                            }
                        }
                    ?>
                    
                    <img src="<?php echo $prof_pic; ?>" alt="Profile" class="header-avatar">
                    <span class="header-username">Hi, <?php echo $_SESSION['username']; ?></span>
                    
                    <span class="separator">|</span>
                    
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a href="admin_dashboard.php" class="header-link" style="color:#ff9f43; font-weight:bold;">Dashboard</a>
                        <a href="user_profile.php" class="header-link">My Profile</a>
                    <?php else: ?>
                        <a href="user_profile.php" class="header-link">Profile</a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="header-link logout-btn"><i class="fa-solid fa-power-off"></i></a>
                </div>

            <?php else: ?>
                <a href="login.php" class="auth-btn"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <a href="signup.php" class="auth-btn sign-up"><i class="fa-solid fa-user-plus"></i> Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="container header-flex">
        <div class="logo-area">
            <a href="index.php">
                <img src="images/logo.png" alt="OWA Logo" class="logo">
            </a>
        </div>
        
        <div class="brand-name">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <h1>ONE WORLD AVIATORS CLUB</h1>
            </a>
            <p class="slogan">Flying Towards Tomorrow. Inspiring Future Aviators Today</p>
        </div>
    </div>
</header>

<nav class="navbar">
    <div class="container">
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="news.php">Latest News</a></li>
            <li><a href="programs.php">Aviation Programs</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="gallery.php">Plane Spotters Gallery</a></li>
            <li><a href="community.php"> The Hangar </a></li>
        </ul>
        <div class="menu-toggle"><i class="fa-solid fa-bars"></i></div>
    </div>
</nav>