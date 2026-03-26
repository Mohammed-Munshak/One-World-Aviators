<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contact-card {
            background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0; overflow: hidden; display: flex; flex-direction: column;
        }
        .contact-info-side {
            background: var(--primary-color); color: white; padding: 40px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .contact-info-side h3 { font-size: 1.8rem; margin-bottom: 20px; color: var(--secondary-color); }
        .info-item-row { display: flex; align-items: center; margin-bottom: 25px; font-size: 1.1rem; }
        .info-item-row i { width: 40px; font-size: 1.2rem; color: var(--secondary-color); }
        
        .contact-form-side { padding: 40px; background: white; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        .form-control:focus { outline: none; border-color: var(--secondary-color); }

        /* Map Section */
        .map-container {
            margin-top: 30px; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        @media (min-width: 768px) {
            .contact-card { flex-direction: row; }
            .contact-info-side { width: 40%; }
            .contact-form-side { width: 60%; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you!</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div style="background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:5px; text-align:center;">
                    Message sent successfully! We will get back to you soon.
                </div>
            <?php endif; ?>

            <div class="contact-card">
                
                <div class="contact-info-side">
                    <h3>Get in Touch</h3>
                    <p style="margin-bottom: 30px; opacity: 0.9;">
                        Have questions about aviation programs, memberships, or just want to say hi? Drop us a message!
                    </p>
                    
                    <div class="info-item-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Narammala, Sri Lanka</span>
                    </div>
                    <div class="info-item-row">
                        <i class="fa-solid fa-envelope"></i>
                        <span>oneworldaviators@gmail.com</span>
                    </div>
                    <div class="info-item-row">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>+94 71 372 9022</span>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <a href="https://www.facebook.com/profile.php?id=100082444987096&mibextid=wwXIfr&mibextid=wwXIfr" style="color: white; font-size: 1.5rem; margin-right: 15px;"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://www.tiktok.com/@one_world_aviators?is_from_webapp=1&sender_device=pc" style="color: white; font-size: 1.5rem; margin-right: 15px;"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://youtube.com/@oneworldaviators?si=kqZ3ByavpH1IMIS7" style="color: white; font-size: 1.5rem;"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="contact-form-side">
                    <h2>Send a Message</h2>
                    <form action="submit_contact.php" method="POST">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Your Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter your name">

                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="Enter your email">

                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Subject</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Subject">

                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Message</label>
                        <textarea name="message" rows="5" class="form-control" required placeholder="How can we help you?"></textarea>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>