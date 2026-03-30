<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific Styles for About Page to match the 'Card' theme */
        .about-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .about-image {
            height: 350px;
            width: 100%;
        }
        
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .about-content-box {
            padding: 40px;
        }
        
        .mission-vision-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .mv-box {
            background: #f8f9fa;
            padding: 20px;
            border-left: 4px solid var(--secondary-color);
            border-radius: 4px;
        }

        /* Hover effect for the new join button */
        .join-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        @media (min-width: 768px) {
            .about-card {
                flex-direction: row;
            }
            .about-image {
                width: 40%;
                height: auto; /* Stretches to match text height */
            }
            .about-content-box {
                width: 60%;
            }
            .mission-vision-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>About Us</h1>
            <p>Flying Towards Tomorrow. Inspiring Future Aviators Today.</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="about-card">
                <div class="about-image">
                    <img src="images/about-team.jpg" alt="OWA Team" onerror="this.src='https://placehold.co/600x400?text=One+World+Aviators'">
                </div>
                
                <div class="about-content-box">
                    <h2 style="color: var(--primary-color); margin-bottom: 15px;">Who We Are</h2>
                    <p style="margin-bottom: 15px; color: #555;">
                        One World Aviators Club is dedicated to guiding all Sri Lankans on their aviation journey: 
                        connecting enthusiasts, future aviators, and professionals with the knowledge and support they need.
                    </p>
                    <p style="margin-bottom: 15px; color: #555;">
                        Whether you are a student looking for the right college, a plane spotter capturing the beauty of flight, 
                        or a professional wanting to stay updated, we are your home in the sky.
                    </p>

                    <div class="mission-vision-grid">
                        <div class="mv-box">
                            <h3 style="color: var(--primary-color); font-size: 1.1rem;"><i class="fa-solid fa-bullseye"></i> Our Mission</h3>
                            <p style="font-size: 0.9rem;">To provide a comprehensive platform that educates, connects, and inspires the next generation of aviators.</p>
                        </div>
                        <div class="mv-box">
                            <h3 style="color: var(--primary-color); font-size: 1.1rem;"><i class="fa-regular fa-lightbulb"></i> Our Vision</h3>
                            <p style="font-size: 0.9rem;">To be the premier hub for aviation culture, news, and education in Sri Lanka.</p>
                        </div>
                    </div>

                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee; text-align: center;">
                        <h3 style="color: var(--primary-color); font-size: 1.3rem; margin-bottom: 10px;">Join Our Community</h3>
                        <p style="color: #666; margin-bottom: 20px;">Passionate about aviation? Become a registered member of our team today.</p>
                        
                        <a href="signup.php" class="join-btn" style="
                            display: inline-block;
                            background-color: var(--primary-color);
                            color: white;
                            padding: 12px 30px;
                            border-radius: 30px;
                            text-decoration: none;
                            font-weight: bold;
                            transition: all 0.3s ease;
                            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                        ">
                            <i class="fa-solid fa-user-plus"></i> Become a Member
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>
</body>
</html>