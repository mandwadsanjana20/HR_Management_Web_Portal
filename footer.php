<!-- footer.php -->
 <!DOCTYPE html>
<html>
<head>
    <title>HR Management Portal</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Optional Animation -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
 <style>
/* Footer Styles */
/* Footer Styling */
.footer {
  background-color: white;
  color: #000;
  padding: 40px 20px 20px;
  font-family: 'Segoe UI', sans-serif;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  max-width: 1200px;
  margin: auto;
}

.footer-section {
  flex: 1 1 220px;
  margin: 20px;
}

.footer-section h2, .footer-section h3 {
  margin-bottom: 15px;
  color: rgb(128, 11, 11);;
}

.footer-section p,
.footer-section li,
.footer-section a {
  color: #333;
  font-size: 14px;
  text-decoration: none;
  margin: 5px 0;
}

.footer-section a:hover {
  color: red;
  text-decoration: underline;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.social-icons {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.social-icons a {
  color: #000;
  font-size: 20px;
  transition: 0.3s ease;
}

.footer-bottom {
  text-align: center;
  padding: 15px 0;
  border-top: 1px solid #444;
  margin-top: 20px;
  font-size: 13px;
  color: #999;
}
</style>
</head>
<body>
<!-- footer.php -->
<!-- footer.php -->
<footer class="footer bg-white text-dark py-5">
  <div class="container">
    <div class="row">

      <!-- Company Info -->
      <div class="col-md-3 mb-4">
        <a href="index.php" class="d-inline-block mb-3">
          <img src="tech.jpg" alt="Logo" width="150" height="100" class="img-fluid">
        </a>
        <p style="font-family: 'Segoe UI', sans-serif; font-size: 14px; color: #333;">
          Empowering businesses through efficient HR services – from recruitment to retirement. Designed to simplify your HR operations with ease and automation.
        </p>
      </div>

      <!-- Quick Links -->
      <div class="col-md-3 mb-4">
        <h5 class="text-danger mb-3">Quick Links</h5>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="index.php" class="text-dark text-decoration-none">Home</a></li>
          <li class="mb-2"><a href="about.php" class="text-dark text-decoration-none">About Us</a></li>
          <li class="mb-2"><a href="job_post.php" class="text-dark text-decoration-none">Job Posts</a></li>
          <li class="mb-2"><a href="contact.php" class="text-dark text-decoration-none">Contact</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="col-md-3 mb-4">
        <h5 class="text-danger mb-3">Contact Us</h5>
        <p class="mb-2"><i class="fas fa-envelope mr-2"></i>support@hrportal.com</p>
        <p class="mb-2"><i class="fas fa-phone mr-2"></i>+91-9876543210</p>
        <p><i class="fas fa-map-marker-alt mr-2"></i>Solapur, Maharashtra, India</p>
      </div>

      <!-- Social Icons -->
      <div class="col-md-3 mb-4">
        <h5 class="text-danger mb-3">Follow Us</h5>
        <div class="d-flex gap-3">
          <a href="#" class="text-dark fs-4" title="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-dark fs-4" title="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-dark fs-4" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="text-dark fs-4" title="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

    </div>

    <div class="footer-bottom text-center border-top pt-3 mt-4" style="border-color: #444 !important; font-size: 13px; color: #999;">
      &copy; <?php echo date("Y"); ?> HR Management Web Portal. All rights reserved.
    </div>
  </div>
</footer>

<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
  // Animate social icons on hover
  document.querySelectorAll('.footer .d-flex a').forEach(icon => {
    icon.addEventListener('mouseover', () => icon.style.transform = 'scale(1.2)');
    icon.addEventListener('mouseout', () => icon.style.transform = 'scale(1)');
  });
</script>

</body>
<html>
