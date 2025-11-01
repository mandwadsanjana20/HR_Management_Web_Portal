<?php
session_start();
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$candidate_email = $_SESSION['candidate_email'] ?? null; // logged in email or null
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {margin:0; padding:0; font-family:'Roboto',sans-serif; background:#f4f6f8;}
    /* Cards above */
    .cards {display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; max-width:1100px; margin:40px auto; padding:0 20px;}
    .card {background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.1); transition:transform 0.3s ease;}
    .card:hover {transform:translateY(-5px);}
    .card img {width:100%; height:200px; object-fit:cover; display:block;}
    .card-content {padding:15px;}
    .card-content h3 {margin:0 0 10px; font-size:18px; color:#333;}
    .card-content p {margin:0; color:#555; font-size:14px;}
    /* Contact + Map Container */
    .contact-map-section {display:flex; justify-content:center; align-items:stretch; max-width:1100px; margin:0 auto 60px; padding:0 20px; gap:30px;}
    .contact-container {background:#ffffff; padding:40px 30px; flex:1; box-shadow:0 8px 20px rgba(0,0,0,0.1); border-radius:12px; transition:transform 0.3s ease;}
    .contact-container:hover {transform:translateY(-5px);}
    .map-container {flex:1; border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
    iframe {width:100%; height:100%; border:0; min-height:400px;}
    h1 {text-align:center; margin-bottom:24px; color:#333333;}
    .input-group {margin-bottom:20px;}
    .input-group label {display:block; margin-bottom:8px; font-weight:bold; color:#333; font-size:14px;}
    .input-group input, .input-group textarea {width:100%; padding:12px 15px; border:1px solid #ccc; border-radius:6px; font-size:15px; transition:border-color 0.3s ease, box-shadow 0.3s ease;}
    .input-group input:focus, .input-group textarea:focus {border-color:#007BFF; box-shadow:0 0 0 3px rgba(0,123,255,0.2); outline:none;}
    button {background:linear-gradient(135deg,#007BFF,#0056b3); color:#fff; padding:14px 0; border:none; cursor:pointer; width:100%; border-radius:6px; font-size:16px; font-weight:bold; transition:background 0.3s ease, transform 0.2s ease;}
    button:hover {background:linear-gradient(135deg,#0056b3,#003f7f); transform:translateY(-2px);}
    .message {background:#d4edda; color:#155724; padding:15px; border:1px solid #c3e6cb; margin-bottom:20px; border-radius:6px; text-align:center; font-weight:bold;}
    @media (max-width:900px) {.contact-map-section{flex-direction:column;} iframe{min-height:300px;}}
  </style>
</head>
<body>
<?php include('navbar.php'); ?>

<!-- Cards Section -->
<div class="cards">
  <div class="card">
    <img src="4.webp" alt="Office Image">
    <div class="card-content">
      <h3>Our Main Office</h3>
      <p>Located in the heart of the city, easily accessible.</p>
    </div>
  </div>
  <div class="card">
    <img src="3.jpg" alt="Team Image">
    <div class="card-content">
      <h3>Meet Our Team</h3>
      <p>Dedicated professionals ready to assist you.</p>
    </div>
  </div>
  <div class="card">
    <img src="8.jpg" alt="Meeting Image">
    <div class="card-content">
      <h3>Client Meetings</h3>
      <p>Modern meeting spaces to discuss your needs.</p>
    </div>
  </div>
</div>

<!-- Contact + Map -->
<div class="contact-map-section">
  <div class="contact-container">
    <h1>Contact Us</h1>
    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form id="contactForm" action="" method="POST">
      <div class="input-group">
        <label for="name">Your Name</label>
        <input type="text" id="name" name="name" required/>
      </div>

      <?php if ($candidate_email): ?>
        <!-- ✅ Logged-in user: email auto-filled & disabled -->
        <div class="input-group">
          <label for="email">Your Email</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($candidate_email) ?>" readonly required/>
        </div>
      <?php else: ?>
        <!-- ❌ New/Not logged in: show email + password -->
        <div class="input-group">
          <label for="email">Your Email</label>
          <input type="email" id="email" name="email" required/>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required/>
        </div>
      <?php endif; ?>

      <div class="input-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" required/>
      </div>
      <div class="input-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" required></textarea>
      </div>
      <button type="submit">Send Message</button>
    </form>
  </div>

  <div class="map-container">
    <!-- Google Map Embed -->
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!..." allowfullscreen="" loading="lazy"></iframe>
  </div>
</div>
<?php include('footer.php'); ?>

</body>
</html>
