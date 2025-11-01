<?php
session_start();
include 'navbar.php';
$conn = new mysqli("localhost", "root", "", "company_db");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HR Management Portal</title>

  <!-- Google Fonts + FontAwesome + Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

  <style>
/* ================================
   Base Styles & Page Animation
================================ */
body {
  background: linear-gradient(135deg, #e0e0e0 0%, #f9f9f9 100%);
  color: #111;
  font-family: 'Poppins', sans-serif;
  margin: 0;
  line-height: 1.6;
}

.page-wrapper {
  opacity: 0;
  transform: translateY(30px);
  filter: blur(5px);
  animation: pageFadeSlideIn 1.2s ease forwards;
}

@keyframes pageFadeSlideIn {
  0% {
    opacity: 0;
    transform: translateY(40px);
    filter: blur(15px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
  }
}

/* ================================
   Fade-up Animation for Sections
================================ */
.fade-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s ease, transform 0.8s ease;
}

.fade-up.visible {
  opacity: 1;
  transform: translateY(0);
}

/* ================================
   Hero Section
================================ */
.hero {
  background: linear-gradient(135deg, #e0e0e0 0%, #f9f9f9 100%);
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
  margin: 40px 20px 0;
  padding: 40px 30px;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 40px;
}

.hero-text {
  max-width: 480px;
  flex: 1 1 480px;
  z-index: 3;
}

.hero h2 {
  font-size: 2.3rem;
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 15px;
  line-height: 1.1;
}

.hero p {
  font-size: 1rem;
  margin-bottom: 30px;
  color: #444;
  line-height: 1.5;
}

.hero a {
  display: inline-block;
  background: #111;
  color: #fff;
  padding: 14px 40px;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.3s ease, color 0.3s ease;
  box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);
}

.hero a:hover {
  background: red;
  color: #111;
  box-shadow: 0 3px 10px rgb(0 0 0 / 0.5);
  border: 1px solid #111;
  text-decoration: none;
}

/* Hero Text Animation */
.hero-text h2,
.hero-text p,
.hero-text a {
  opacity: 0;
  transform: translateY(40px) scale(0.95);
  animation: fadePopUp 1s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

.hero-text h2 { animation-delay: 0.2s; }
.hero-text p { animation-delay: 0.5s; }
.hero-text a { animation-delay: 0.8s; }

@keyframes fadePopUp {
  0% { opacity: 0; transform: translateY(40px) scale(0.95); }
  60% { opacity: 1; transform: translateY(-5px) scale(1.02); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* Hero Floating Circles */
.hero::before,
.hero::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  opacity: 0.1;
  background: #000;
  animation: float 5s ease-in-out infinite alternate;
  pointer-events: none;
}

.hero::before {
  width: 280px; height: 280px;
  top: -90px; left: -90px;
}

.hero::after {
  width: 230px; height: 230px;
  bottom: -90px; right: -90px;
}

@keyframes float {
  0% { transform: translateY(0);}
  100% { transform: translateY(25px);}
}

/* Hero Image with Heartbeat */
.hero-image {
  flex: 1 1 480px;
  max-width: 480px;
  position: relative;
}

.hero-image-shade {
  position: absolute;
  top: 15px;
  left: 15px;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.1);
  border-radius: 12px;
  filter: blur(15px);
  z-index: 1;
  pointer-events: none;
}

.hero-image img {
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  position: relative;
  z-index: 2;
  width: 110%;
  height: 150%;
  display: block;
  animation: heartbeat 2s ease-in-out infinite;
}

@keyframes heartbeat {
  0%, 100% { transform: scale(1); }
  25% { transform: scale(1.05); }
  50% { transform: scale(1.05); }
  75% { transform: scale(1.05); }
}

/* ================================
   Improved Job Cards for Home Page
================================ */

.jobs {
  padding: 80px 20px;
  background: #fff;
  border-radius: 12px;
  max-width: 1200px;
  margin: 40px auto;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.jobs h5 {
  text-transform: uppercase;
  letter-spacing: 3px;
  color: #111;
  font-weight: 700;
  margin-bottom: 10px;
  opacity: 0.6;
}

.jobs h1 {
  font-weight: 700;
  margin-bottom: 50px;
  color: #111;
}

.job-card {
  border-radius: 12px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.job-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
}

.job-card .card-body {
  display: flex;
  flex-direction: column;
}

.job-card .card-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #111;
  margin-bottom: 10px;
}

.job-card .card-text {
  font-size: 0.95rem;
  color: #555;
  flex-grow: 1;
}

.job-card .apply-btn {
  border-radius: 50px;
  font-weight: 600;
  transition: background 0.3s ease, transform 0.3s ease;
}

.job-card .apply-btn:hover {
  background: #dc3545;
  transform: translateX(5px);
}


/* ================================
   Info Section
================================ */
.info-header {
  max-width: 1200px;
  margin: 10px auto;
  padding: 0 20px;
  text-align: center;
}

.info-header h2 {
  font-weight: 700;
  color: #111;
  font-size: 2.5rem;
  margin-bottom: 5px;
  position: relative;
  display: inline-block;
}

.info-header h2::after {
  content: '';
  display: block;
  width: 60px;
  height: 3px;
  background-color: #111;
  margin: 8px auto 0 auto;
  border-radius: 2px;
}

.info-header p {
  font-size: 1.1rem;
  color: #444;
  max-width: 700px;
  margin: 0 auto 40px auto;
  line-height: 1.5;
}

.info {
  padding: 20px 20px;
  max-width: 1200px;
  margin: 20px auto;
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  align-items: flex-start;
}

.info-left, .info-right {
  flex: 1 1 400px;
}

.info-box {
  background: #fff;
  border: 1px solid #ddd;
  padding: 25px;
  border-left: 5px solid #111;
  border-radius: 8px;
  margin-bottom: 20px;
  transition: box-shadow 0.3s ease, border-color 0.3s ease;
}

.info-box:hover {
  border-color: #444;
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

.info-box h4 {
  margin: 0 0 10px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #111;
  display: flex;
  align-items: center;
  gap: 10px;
}

.info-box h4 i {
  color: #111;
  font-size: 1.3rem;
}

.info-box p {
  font-size: 0.95rem;
  color: #555;
  line-height: 1.4;
}

.info-right img {
  width: 100%;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  object-fit: cover;
  max-height: 350px;
  display: block;
}

.quote {
  margin-top: 20px;
  font-style: italic;
  color: #666;
  background: #f5f5f5;
  padding: 15px 20px;
  border-radius: 8px;
  border-left: 4px solid #111;
}

/* Responsive */
@media (max-width: 768px) {
  .hero {
    padding: 30px 20px;
    flex-direction: column;
  }
  .hero-text, .hero-image {
    flex: 1 1 100%;
    max-width: 100%;
  }
  .info {
    flex-direction: column;
    padding: 40px 20px;
  }
}

  </style>
</head>
<body>
<div class="page-wrapper">
  <!-- HERO -->
  <section class="hero fade-up">
    <div class="container d-flex flex-wrap align-items-center justify-content-center" style="gap: 40px;">
      <div class="hero-text" style="max-width: 500px; flex: 1;">
        <h2>Empowering Your Workforce, Simplifying HR</h2>
        <p>Welcome to TalentTech Solutions — your trusted partner in innovative HR technology. We specialize in delivering cutting-edge tools to streamline recruitment, employee management, and professional development. Our platform harnesses AI and data-driven insights to help your company attract, retain, and grow top talent with ease and efficiency.</p>
        <a href="register.php">Get Started</a>
      </div>
      <div class="hero-image">
        <div class="hero-image-shade"></div>
        <img src="team.jpg" alt="HR Illustration" />
      </div>
    </div>
  </section>

  <!-- JOBS Section: Show 3 most recent jobs in Bootstrap cards -->
<section class="jobs fade-up">
  <div class="container">
    <h5>New Openings</h5>
    <h1>Recently Added Positions</h1>
    <div class="row">
      <?php
        $sql = "SELECT * FROM jobs ORDER BY posted_date DESC LIMIT 3";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<div class="col-md-4 mb-4 d-flex">';
            echo '  <div class="card job-card shadow-sm w-100">';
            echo '    <div class="card-body d-flex flex-column">';
            echo '      <h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>';
            echo '      <p class="card-text">' . htmlspecialchars(substr($row['description'], 0, 100)) . '...</p>';
            echo '      <small class="text-muted mb-2">Posted on: ' . htmlspecialchars($row['posted_date']) . '</small>';
            echo '      <a href="apply.php?job_id=' . urlencode($row['job_id']) . '" class="btn btn-dark mt-auto apply-btn">Apply Now <i class="fas fa-arrow-right"></i></a>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';
          }
        } else {
          echo "<div class='col-12'><p class='text-center'>No current job postings.</p></div>";
        }
      ?>
    </div>
  </div>
</section>


  <!-- INFO -->
  <section class="info fade-up">
    <div class="info-header">
      <h2>About Us</h2>
      <p>We are dedicated to connecting talented professionals with leading companies, streamlining the hiring process to build strong, dynamic teams. Our platform provides smart tools for sourcing, managing, and growing your workforce efficiently.</p>
    </div>
    <div class="info-left">
      <div class="info-box">
        <h4><i class="fas fa-users"></i> Find Top Talent</h4>
        <p>Access the best candidates with streamlined sourcing and smart hiring tools.</p>
      </div>
      <div class="info-box">
        <h4><i class="fas fa-clock"></i> Save Time & Money</h4>
        <p>Automate repetitive tasks and focus on what truly matters — growing your team.</p>
      </div>
      <div class="info-box">
        <h4><i class="fas fa-handshake"></i> Build Strong Teams</h4>
        <p>Promote collaboration, strengthen your culture, and scale your workforce confidently.</p>
      </div>
    </div>
    <div class="info-right">
      <img src="9.jpg" alt="Team collaboration" />
      <div class="quote">“To win in the marketplace you must first win in the workplace.” — Doug Conant</div>
    </div>
  </section>
  
<!-- Career Growth Opportunities Section -->
<section class="career-growth fade-up" style="max-width: 1200px; margin: 40px auto; padding: 20px;">
  <div class="info-header text-center mb-4">
    <h2>Career Growth Opportunities</h2>
    <p>We are committed to your professional development and success. Here’s how we support your growth:</p>
  </div>

  <div class="row text-center">
    <div class="col-md-4 mb-4">
      <div class="info-box p-4 h-100">
        <i class="fas fa-chalkboard-teacher fa-3x mb-3" style="color:#111;"></i>
        <h4>Training & Workshops</h4>
        <p>Regular skill-building sessions to enhance your expertise and keep you ahead.</p>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="info-box p-4 h-100">
        <i class="fas fa-level-up-alt fa-3x mb-3" style="color:#111;"></i>
        <h4>Promotion Paths</h4>
        <p>Clear and transparent career paths so you know what’s next for you.</p>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="info-box p-4 h-100">
        <i class="fas fa-users-cog fa-3x mb-3" style="color:#111;"></i>
        <h4>Mentorship Programs</h4>
        <p>Connect with experienced leaders to guide your professional journey.</p>
      </div>
    </div>
  </div>
</section>
  <?php include 'footer.php'; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const fadeEls = document.querySelectorAll('.fade-up');

    function checkFade() {
      const triggerBottom = window.innerHeight * 0.9;
      fadeEls.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < triggerBottom) {
          el.classList.add('visible');
        }
      });
    }

    window.addEventListener('scroll', checkFade);
    checkFade();
  });
</script>

</body>
</html>