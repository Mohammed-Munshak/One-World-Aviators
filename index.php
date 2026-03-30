<?php
// 1. Start Session and Connect to Database
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One World Aviators Club - Flying Towards Tomorrow</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================
           HOME PAGE SPECIFIC STYLES
           ========================================= */
        
        /* 1. HERO SECTION WITH IMAGE BACKGROUND */
        .hero-section {
            /* Make sure 'home-hero.jpg' exists in your images folder */
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/home-hero.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 150px 20px;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }

        .hero-content h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        /* 2. NEW EVENT CARD DESIGN */
        .event-card-home {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            display: flex;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid #eee;
            transition: transform 0.3s ease;
        }

        .event-card-home:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .event-home-img {
            width: 250px;
            flex-shrink: 0;
            position: relative;
        }

        .event-home-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Date Badge overlaid on image */
        .date-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .db-month { display: block; font-size: 0.8rem; font-weight: bold; color: #555; text-transform: uppercase; }
        .db-day { display: block; font-size: 1.5rem; font-weight: bold; color: var(--primary-color); line-height: 1; }

        .event-home-details {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .event-home-details h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .event-meta-home {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }

        .event-meta-home i { color: var(--secondary-color); margin-right: 5px; }

        /* Responsive */
        @media (max-width: 768px) {
            .event-card-home { flex-direction: column; }
            .event-home-img { width: 100%; height: 200px; }
        }

        .virtual-crew-section {
  position: relative;
  overflow: hidden;
  padding: 70px 20px;
  background:
    radial-gradient(circle at top left, rgba(127, 90, 240, 0.18), transparent 32%),
    radial-gradient(circle at bottom right, rgba(44, 182, 125, 0.18), transparent 28%),
    linear-gradient(135deg, #07111f 0%, #0d1b2e 45%, #132742 100%);
  isolation: isolate;
}

.virtual-crew-container {
  position: relative;
  max-width: 1180px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 56px;
  padding: 38px;
  border-radius: 32px;
  background: linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.06));
  border: 1px solid rgba(255,255,255,0.14);
  box-shadow:
    0 24px 80px rgba(0, 0, 0, 0.35),
    inset 0 1px 0 rgba(255,255,255,0.12);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  animation: virtualCrewFadeUp 1s ease both;
}

.virtual-crew-container::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 32px;
  padding: 1px;
  background: linear-gradient(135deg, rgba(255,255,255,0.34), rgba(255,255,255,0.04), rgba(44,182,125,0.25));
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
          mask-composite: exclude;
  pointer-events: none;
}

.virtual-crew-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(10px);
  z-index: -1;
  animation: virtualCrewFloat 7s ease-in-out infinite;
}

.virtual-crew-glow-1 {
  width: 260px;
  height: 260px;
  top: 18px;
  left: 8%;
  background: rgba(127, 90, 240, 0.22);
}

.virtual-crew-glow-2 {
  width: 220px;
  height: 220px;
  right: 10%;
  bottom: 12px;
  background: rgba(44, 182, 125, 0.18);
  animation-delay: 1.5s;
}

.virtual-crew-image-wrap {
  position: relative;
  flex: 0 0 310px;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: virtualCrewImageIn 1.2s ease both;
}

.virtual-crew-image-ring {
  position: absolute;
  width: 300px;
  height: 300px;
  border-radius: 30px;
  background:
    linear-gradient(135deg, rgba(127,90,240,0.35), rgba(44,182,125,0.18)),
    rgba(255,255,255,0.08);
  transform: rotate(-8deg);
  box-shadow:
    0 20px 45px rgba(0,0,0,0.25),
    inset 0 1px 0 rgba(255,255,255,0.12);
  animation: virtualCrewPulse 5s ease-in-out infinite;
}

.virtual-crew-image-shine {
  position: absolute;
  width: 170px;
  height: 370px;
  background: linear-gradient(180deg, rgba(255,255,255,0.0), rgba(255,255,255,0.22), rgba(255,255,255,0.0));
  transform: rotate(22deg);
  left: -20px;
  top: -25px;
  pointer-events: none;
  animation: virtualCrewShine 4.2s ease-in-out infinite;
}

.virtual-crew-image {
  position: relative;
  width: 270px;
  height: 340px;
  border-radius: 28px;
  overflow: hidden;
  background: rgba(255,255,255,0.08);
  box-shadow:
    0 22px 50px rgba(0,0,0,0.32),
    inset 0 1px 0 rgba(255,255,255,0.2);
  transform: translateY(0);
  transition: transform 0.45s ease, box-shadow 0.45s ease;
}

.virtual-crew-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transform: scale(1.03);
  transition: transform 0.6s ease;
}

.virtual-crew-container:hover .virtual-crew-image {
  transform: translateY(-8px) rotate(-1deg);
  box-shadow:
    0 28px 65px rgba(0,0,0,0.42),
    0 0 30px rgba(127,90,240,0.18);
}

.virtual-crew-container:hover .virtual-crew-image img {
  transform: scale(1.08);
}

.virtual-crew-content {
  flex: 1;
  color: #ffffff;
  animation: virtualCrewFadeRight 1.1s ease both;
}

.virtual-crew-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  padding: 8px 16px;
  border-radius: 999px;
  background: linear-gradient(135deg, rgba(127,90,240,0.24), rgba(44,182,125,0.18));
  border: 1px solid rgba(255,255,255,0.15);
  color: #dff8ee;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.virtual-crew-badge::before {
  content: "✦";
  color: #ffffff;
  font-size: 12px;
}

.virtual-crew-content h2 {
  margin: 0 0 18px;
  font-size: clamp(32px, 4vw, 52px);
  line-height: 1.06;
  font-weight: 800;
  letter-spacing: -1.2px;
  color: #ffffff;
  text-shadow: 0 8px 30px rgba(0,0,0,0.22);
}

.virtual-crew-content h2 span {
  display: inline-block;
  background: linear-gradient(135deg, #ffffff 0%, #b7c9ff 32%, #8ef1cb 72%, #ffffff 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-size: 200% auto;
  animation: virtualCrewTextShift 4.5s linear infinite;
}

.virtual-crew-text {
  max-width: 700px;
  margin: 0 0 28px;
  font-size: 17px;
  line-height: 1.85;
  color: rgba(240, 246, 255, 0.9);
  text-shadow: 0 4px 18px rgba(0,0,0,0.14);
}

.virtual-crew-text strong {
  color: #ffffff;
  font-weight: 700;
}

.virtual-crew-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
}

.virtual-crew-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 210px;
  padding: 15px 28px;
  border-radius: 999px;
  text-decoration: none;
  font-size: 15px;
  font-weight: 700;
  color: #ffffff;
  background: linear-gradient(135deg, #1877f2 0%, #4f46e5 100%);
  box-shadow:
    0 14px 30px rgba(24, 119, 242, 0.28),
    inset 0 1px 0 rgba(255,255,255,0.18);
  overflow: hidden;
  transition: transform 0.35s ease, box-shadow 0.35s ease, letter-spacing 0.35s ease;
}

.virtual-crew-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -120%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
  transform: skewX(-22deg);
}

.virtual-crew-btn:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow:
    0 20px 42px rgba(24, 119, 242, 0.35),
    0 0 26px rgba(79, 70, 229, 0.22);
  letter-spacing: 0.2px;
}

.virtual-crew-btn:hover::before {
  animation: virtualCrewBtnShine 0.9s ease;
}

@keyframes virtualCrewFadeUp {
  from {
    opacity: 0;
    transform: translateY(34px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes virtualCrewFadeRight {
  from {
    opacity: 0;
    transform: translateX(35px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes virtualCrewImageIn {
  from {
    opacity: 0;
    transform: translateX(-40px) scale(0.92);
  }
  to {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

@keyframes virtualCrewFloat {
  0%, 100% {
    transform: translateY(0px) scale(1);
  }
  50% {
    transform: translateY(-16px) scale(1.04);
  }
}

@keyframes virtualCrewPulse {
  0%, 100% {
    transform: rotate(-8deg) scale(1);
  }
  50% {
    transform: rotate(-5deg) scale(1.03);
  }
}

@keyframes virtualCrewShine {
  0% {
    opacity: 0;
    transform: translateX(-120px) rotate(22deg);
  }
  20% {
    opacity: 0.55;
  }
  60% {
    opacity: 0.2;
  }
  100% {
    opacity: 0;
    transform: translateX(320px) rotate(22deg);
  }
}

@keyframes virtualCrewTextShift {
  0% {
    background-position: 0% center;
  }
  100% {
    background-position: 200% center;
  }
}

@keyframes virtualCrewBtnShine {
  0% {
    left: -120%;
  }
  100% {
    left: 140%;
  }
}

@media (max-width: 991px) {
  .virtual-crew-container {
    gap: 34px;
    padding: 30px;
  }

  .virtual-crew-image-wrap {
    flex-basis: 260px;
  }

  .virtual-crew-image-ring {
    width: 255px;
    height: 255px;
  }

  .virtual-crew-image {
    width: 230px;
    height: 300px;
  }

  .virtual-crew-text {
    font-size: 16px;
    line-height: 1.75;
  }
}

@media (max-width: 768px) {
  .virtual-crew-section {
    padding: 55px 16px;
  }

  .virtual-crew-container {
    flex-direction: column;
    text-align: center;
    gap: 28px;
    padding: 28px 20px;
    border-radius: 26px;
  }

  .virtual-crew-content h2 {
    font-size: 32px;
  }

  .virtual-crew-text {
    max-width: 100%;
    font-size: 15px;
  }

  .virtual-crew-actions {
    justify-content: center;
  }

  .virtual-crew-image-wrap {
    flex: none;
  }

  .virtual-crew-image-ring {
    width: 240px;
    height: 240px;
  }

  .virtual-crew-image {
    width: 215px;
    height: 285px;
    border-radius: 24px;
  }
}

@media (max-width: 480px) {
  .virtual-crew-container {
    padding: 22px 16px;
  }

  .virtual-crew-badge {
    font-size: 11px;
    padding: 7px 12px;
  }

  .virtual-crew-content h2 {
    font-size: 27px;
  }

  .virtual-crew-text {
    font-size: 14px;
    line-height: 1.7;
  }

  .virtual-crew-btn {
    min-width: 100%;
    padding: 14px 20px;
  }

  .virtual-crew-image-ring {
    width: 215px;
    height: 215px;
  }

  .virtual-crew-image {
    width: 195px;
    height: 255px;
  }
}
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="hero-section">
        <div class="container hero-content">
            <h2>Welcome to One World Aviators Club</h2>
            <p>
                One World Aviators Club is dedicated to guiding all Sri Lankans on their aviation journey: 
                connecting enthusiasts, future aviators, and professionals with the knowledge and support they need.
            </p>
            <a href="about.php" class="btn btn-primary" style="padding: 12px 30px; font-size: 1.1rem;">Learn More</a>
        </div>
    </section>

    <section class="latest-news-section">
        <div class="container">
            <div class="section-header">
                <h2>Latest News</h2>
            </div>
            
            <div class="news-grid">
                <?php
                $news_sql = "SELECT * FROM latest_news ORDER BY published_date DESC LIMIT 3";
                $news_result = $conn->query($news_sql);

                if ($news_result->num_rows > 0) {
                    while($row = $news_result->fetch_assoc()) {
                        $img_src = !empty($row['image_path']) ? $row['image_path'] : 'images/default_news.jpg';
                        echo '
                        <article class="news-card">
                            <div class="news-image">
                                <img src="'.$img_src.'" alt="'.$row['headline'].'">
                            </div>
                            <div class="news-content">
                                <span class="news-date"><i class="fa-regular fa-calendar"></i> '.date("F j, Y", strtotime($row['published_date'])).'</span>
                                <h3>'.$row['headline'].'</h3>
                                <p>'.nl2br($row['content']).'</p>
                            </div>
                        </article>';
                    }
                } else {
                    echo '<p class="no-data">No news updates available at the moment. Stay tuned!</p>';
                }
                ?>
            </div>

            <div class="view-all-container">
                <a href="news.php" class="btn-view-all">
                    View All Aviation News <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="shortcuts-section">
        <div class="container">
            <div class="shortcuts-grid">
                <div class="shortcut-card">
                    <div class="icon-box"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3>Aviation Colleges</h3>
                    <p>Gather all aviation colleges and course details from Sri Lanka.</p>
                    <a href="colleges.php" class="btn btn-secondary">Take Off</a>
                </div>
                <div class="shortcut-card">
                    <div class="icon-box"><i class="fa-solid fa-camera"></i></div>
                    <h3>Plane Spotters Community</h3>
                    <p>The place where all the spotters gather around Sri Lanka.</p>
                    <a href="gallery.php" class="btn btn-secondary">Take Off</a>
                </div>
                <div class="shortcut-card">
    <div class="icon-box"><i class="fa-solid fa-comments"></i></div>
    
    <h3>The Hangar</h3>
    
    <p>Ask questions, share aviation insights, and discuss with the aviation community.</p>
    
    <a href="community.php" class="btn btn-secondary">Enter Hangar</a>
</div>
            </div>
        </div>
    </section>

    <section class="programs-section">
        <div class="container">
            <h2>Upcoming Aviation Programs & Events</h2>
            
            <div class="events-list">
                <?php
                // Fetch next 2 upcoming events
                $event_sql = "SELECT * FROM aviation_programs WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 2";
                $event_result = $conn->query($event_sql);

                if ($event_result->num_rows > 0) {
                    while($row = $event_result->fetch_assoc()) {
                        $e_img = !empty($row['image_path']) ? $row['image_path'] : 'images/default_event.jpg';
                        $e_date_month = date("M", strtotime($row['event_date']));
                        $e_date_day = date("d", strtotime($row['event_date']));

                        echo '
                        <div class="event-card-home">
                            <div class="event-home-img">
                                <img src="'.$e_img.'" alt="'.$row['title'].'">
                                <div class="date-badge">
                                    <span class="db-month">'.$e_date_month.'</span>
                                    <span class="db-day">'.$e_date_day.'</span>
                                </div>
                            </div>
                            
                            <div class="event-home-details">
                                <h3>'.$row['title'].'</h3>
                                
                                <div class="event-meta-home">
                                    <span><i class="fa-regular fa-clock"></i> '.date("h:i A", strtotime($row['event_time'])).'</span>
                                    <span><i class="fa-solid fa-location-dot"></i> '.$row['venue'].'</span>
                                </div>
                                
                                <p style="color:#555; font-size:0.95rem; margin-bottom:15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    '.$row['description'].'
                                </p>
                                
                                <a href="programs.php" class="btn btn-secondary" style="align-self: flex-start;">Board Now</a>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-data">No upcoming events scheduled. Check back soon!</p>';
                }
                ?>
            </div>
            
            <div class="center-btn">
                <a href="programs.php" class="view-all-link">View All Programs <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <section class="virtual-crew-section">
  <div class="virtual-crew-glow virtual-crew-glow-1"></div>
  <div class="virtual-crew-glow virtual-crew-glow-2"></div>

  <div class="virtual-crew-container">
    <div class="virtual-crew-image-wrap">
      <div class="virtual-crew-image-ring"></div>
      <div class="virtual-crew-image-shine"></div>
      <div class="virtual-crew-image">
        <img src="images/anya.png" alt="Virtual Cabin Crew Anya">
      </div>
    </div>

    <div class="virtual-crew-content">
      <span class="virtual-crew-badge">Sri Lanka’s First</span>

      <h2>
        Meet Our
        <span>Virtual Cabin Crew</span>
      </h2>

      <p class="virtual-crew-text">
        ශ්‍රී ලංකාවේ ප්‍රථම <strong>Virtual Cabin Crew</strong> අත්දැකීම සමඟ එක්වන්න.
        නවතම තොරතුරු සඳහා ඇයව follow කරන්න.
      </p>

      <div class="virtual-crew-actions">
        <a href="https://www.facebook.com/https://web.facebook.com/profile.php?id=61587276162555" target="_blank" class="virtual-crew-btn">
          Follow on Facebook
        </a>
      </div>
    </div>
  </div>
</section>
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>

</body>
</html>