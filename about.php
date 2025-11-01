<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us</title>

  <!-- Google Fonts + FontAwesome + Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

  <style>
    body {
      background: linear-gradient(135deg, #e0e0e0 0%, #f9f9f9 100%);
      color: #111;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      line-height: 1.6;
      padding: 0;
    }

    .page-wrapper {
      opacity: 0;
      transform: translateY(30px);
      filter: blur(5px);
      animation: pageFadeSlideIn 1.2s ease forwards;
      padding: 40px 30px;
      max-width: 1300px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.05);
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

    /* Fade-up Animation */
    .fade-up {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .fade-up.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* About Header */
    .about-header {
      text-align: center;
      margin-bottom: 50px;
    }

    .about-header h1 {
      font-weight: 700;
      font-size: 3rem;
      color: #111;
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
    }

    .about-header h1::after {
      content: '';
      display: block;
      width: 100px;
      height: 5px;
      background-color: #111;
      margin: 10px auto 0 auto;
      border-radius: 3px;
    }

    .about-header p {
      color: #555;
      font-size: 1.2rem;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.6;
    }

    /* Sections */
    .section {
      margin-bottom: 70px;
      padding-top: 15px;
      padding-bottom: 15px;
    }

    .section h2 {
      font-weight: 700;
      font-size: 2.2rem;
      color: #111;
      margin-bottom: 20px;
      border-left: 6px solid #111;
      padding-left: 20px;
    }

    .section p {
      font-size: 1.1rem;
      color: #444;
      line-height: 1.7;
      max-width: 820px;
      margin-bottom: 20px;
    }

    /* Flex container for text + image */
    .section-flex {
      display: flex;
      align-items: flex-start;
      gap: 0; /* increased gap between text and image */
      max-width: 1020px;
      margin: 0 auto 0px auto;
    }

    .section-content {
      flex: 1;
      padding-right: 15px;
    }

    .section-content ul {
      margin-left: 1.4rem;
      margin-bottom: 1.2rem;
      padding-left: 0;
      list-style-type: disc;
      font-size: 1.05rem;
      color: #444;
    }

    .section-content ul li {
      margin-bottom: 10px;
    }

    .section-image {
      flex: 1;
      max-width: 450px; /* slightly bigger image */
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .section-image img {
      width: 100%;
      max-height: 320px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .section-image img:hover {
      transform: scale(1.03);
    }

    /* Team grid */
    .team-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 40px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .team-member {
      background: #fafafa;
      border-radius: 14px;
      padding: 30px 25px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.07);
      transition: box-shadow 0.3s ease;
    }

    .team-member:hover {
      box-shadow: 0 8px 30px rgba(0,0,0,0.18);
    }

    .team-member img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 18px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.12);
      transition: box-shadow 0.3s ease;
    }

    .team-member h4 {
      margin: 15px 0 8px;
      color: #111;
      font-weight: 700;
      font-size: 1.3rem;
    }

    .team-member p {
      color: #666;
      font-size: 1rem;
      margin-bottom: 15px;
    }

    /* Quote */
    .quote {
      font-style: italic;
      color: #666;
      background: #f5f5f5;
      padding: 25px 30px;
      border-radius: 10px;
      border-left: 6px solid #111;
      max-width: 720px;
      margin: 50px auto 0 auto;
      text-align: center;
      font-size: 1.2rem;
      line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .page-wrapper {
        margin: 20px;
        padding: 30px 20px;
      }
      .team-grid {
        grid-template-columns: 1fr 1fr;
      }
      .section-flex {
        flex-direction: column;
        gap: 30px;
        max-width: 100%;
      }
      .section-content {
        padding-right: 0;
      }
      .section-image {
        max-width: 100%;
        margin-top: 15px;
      }
      .section-image img {
        max-height: none;
      }
    }

    @media (max-width: 480px) {
      .team-grid {
        grid-template-columns: 1fr;
      }
      .about-header h1 {
        font-size: 2.4rem;
      }
      .section h2 {
        font-size: 1.8rem;
        padding-left: 12px;
        border-left-width: 4px;
      }
    }
  </style>
</head>
<body>
  <?php include('navbar.php'); ?>

  <div class="page-wrapper fade-up">

    <header class="about-header">
      <h1>About TalentTech Solutions</h1>
      <p>Your Partner in Innovative HR Technology and Workforce Empowerment</p>
    </header>

    <section class="section">
      <h2>Our Mission</h2>
      <div class="section-flex">
        <div class="section-content">
          <p>At TalentTech Solutions, we are committed to transforming the HR landscape by providing advanced, AI-driven tools that make recruitment, employee management, and professional growth seamless and effective. Our mission is to empower organizations of all sizes to build stronger, more engaged teams through smart technology.</p>
        </div>
        <div class="section-image">
          <img src="team.jpg" alt="Our Mission" />
        </div>
      </div>
    </section>

    <section class="section">
      <h2>What We Do</h2>
      <div class="section-flex">
        <div class="section-content">
          <p>We deliver an integrated platform designed to simplify hiring and workforce management. From sourcing top candidates to automating routine HR tasks, our solutions help businesses save time, reduce costs, and foster a positive workplace culture. Our data-driven insights enable smarter decisions that boost retention and productivity.</p>
          <p>Our platform includes:</p>
          <ul>
            <li>Intelligent candidate sourcing and applicant tracking</li>
            <li>Employee performance management and analytics</li>
            <li>Learning and development tools tailored to career growth</li>
            <li>Automated workflows for HR compliance and payroll</li>
          </ul>
        </div>
        <div class="section-image">
          <img src="9.jpg" alt="What We Do" />
        </div>
      </div>
    </section>

    <section class="section">
      <h2>Meet Our Team</h2>
      <div class="team-grid">
        <div class="team-member">
          <img src="g3.jpg" alt="Alice Johnson - CEO" />
          <h4>Alice Johnson</h4>
          <p>Chief Executive Officer</p>
        </div>
        <div class="team-member">
          <img src="31.jpg" alt="Michael Lee - CTO" />
          <h4>Michael Lee</h4>
          <p>Chief Technology Officer</p>
        </div>
        <div class="team-member">
          <img src="17.jpg" alt="Sara Patel - Head of HR" />
          <h4>Sara Patel</h4>
          <p>Head of Human Resources</p>
        </div>
      </div>
    </section>

    <section class="quote">
      “Great vision without great people is irrelevant.” — Jim Collins
    </section>

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

  <?php include 'footer.php'; ?>

</body>
</html>
