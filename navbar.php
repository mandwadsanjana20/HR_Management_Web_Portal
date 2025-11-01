<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Home Page</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Custom navbar overrides */
    .navbar-custom {
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      padding: 0.75rem 2.5rem;
    }

    .navbar-brand img {
      height: 45px;
    }

    /* Nav link styles */
    .nav-link {
      font-weight: 600;
      font-size: 1.05rem;
      color: #333 !important;
      transition: color 0.3s ease;
    }
    .nav-link:hover {
      color: #e63946 !important;
      text-decoration: none;
    }

    /* Search bar custom style */
    .search-bar input[type="text"] {
      border-radius: 50px 0 0 50px;
      border: 1px solid #ddd;
      padding: 8px 14px;
      font-size: 15px;
      outline: none;
    }

    .search-bar button {
      border-radius: 0 50px 50px 0;
      background: #000;
      border: none;
      color: #fff;
      padding: 8px 14px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .search-bar button:hover {
      background: red;
    }

    .icon-button {
      font-size: 20px;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      color: #333;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      background: none;
      border: none;
      margin-left: 15px;
      cursor: pointer;
      transition: 0.3s;
      text-decoration: none;
    }

    .icon-button:hover {
      background: #e63946;
      color: #fff;
      transform: scale(1.1);
    }

    /* Responsive tweaks */
    @media (max-width: 900px) {
      .navbar-custom {
        padding: 1rem 1.5rem;
      }
      .icon-button {
        margin-left: 0;
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><img src="tech.jpg" alt="Logo"></a>

    <!-- Toggler for collapse on mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarContent">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php if($currentPage == 'index.php') echo 'active'; ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if($currentPage == 'about.php') echo 'active'; ?>" href="about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if($currentPage == 'job_post.php') echo 'active'; ?>" href="job_post.php">Job Posts</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if($currentPage == 'contact.php') echo 'active'; ?>" href="contact.php">Contact</a>
        </li>
      </ul>
    </div>

    <div class="d-flex align-items-center">
      <?php if ($currentPage === 'job_post.php'): ?>
        <form class="search-bar d-flex me-3" method="get" action="job_post.php">
          <input type="text" name="query" placeholder="Search jobs..." 
                 value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
          <button type="submit"><i class="fas fa-search"></i></button>
        </form>
      <?php endif; ?>

      <?php if (isset($_SESSION['employee_email']) || isset($_SESSION['candidate_email'])): ?>
<?php
$profileLink = '#'; // default
$logoutLink  = 'logout.php';

if (isset($_SESSION['candidate_email'])) {
    $profileLink = 'candidate_dashboard.php';
    $logoutLink  = 'logout.php?role=candidate';
} elseif (isset($_SESSION['employee_email'])) {
    $profileLink = 'employee_dashboard.php';
    $logoutLink  = 'logout.php?role=employee';
} elseif (isset($_SESSION['admin_id'])) {
    $profileLink = 'admin_dashboard.php';
    $logoutLink  = 'logout.php?role=admin';
}
?>

<!-- Profile Button -->
<a href="<?php echo $profileLink; ?>" class="icon-button" title="Profile">
    <i class="fas fa-user"></i>
</a>

<!-- Logout Button -->
<a href="<?php echo $logoutLink; ?>" class="icon-button" title="Logout">
    <i class="fas fa-sign-out-alt"></i>
</a>
      <?php else: ?>
        <a href="login1.php" class="icon-button" title="Login"><i class="fas fa-sign-in-alt"></i></a>
        <a href="register.php" class="icon-button" title="Register"><i class="fas fa-user-plus"></i></a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Bootstrap JS Bundle (with Popper) for navbar toggler -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
