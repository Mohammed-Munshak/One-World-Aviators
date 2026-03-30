<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sri Lankan Aviation - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific Holding Page Styles */
        .holding-pattern-container {
            text-align: center;
            padding: 100px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 40px auto;
            border: 1px solid #eee;
        }
        .holding-icon {
            font-size: 5rem;
            color: var(--secondary-color);
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }
        .holding-title {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .holding-text {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }
        
        /* Floating Animation */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Sri Lankan Aviation</h1>
            <p>Explore the local skies</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="holding-pattern-container">
                <i class="fa-solid fa-plane-slash holding-icon"></i>
                
                <h2 class="holding-title">Awaiting Clearance</h2>
                
                <p class="holding-text">
                    This terminal is currently under construction.<br>
                    <strong>We are taxiing to the runway and waiting for ATC commands to take off.</strong>
                </p>
                
                <a href="index.php" class="btn btn-primary">Return to Base (Home)</a>
            </div>
        </div>
    </section>
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>
</body>
</html>