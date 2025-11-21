<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$fullname = $isLoggedIn ? $_SESSION['fullname'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home | Movie Poll</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: url('images/movie-bg.jpg') no-repeat center center/cover;
      height: 100vh;
      color: white;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    nav {
      width: 100%;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 10;
    }

    nav .logo {
      font-size: 1.8rem;
      font-weight: 600;
      color: skyblue;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 25px;
      align-items: center;
      margin: 0;
      padding: 0;
    }

    nav ul li a {
      text-decoration: none;
      color: white;
      font-weight: 500;
      transition: 0.3s;
    }

    nav ul li a:hover {
      color: skyblue;
    }

    /* username styles */
    .nav-user {
      color: white;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    .nav-user .name {
      color: skyblue;
      font-weight: 700;
    }

    /* Hero Section */
    .hero {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: rgba(0, 0, 0, 0.6);
      padding: 60px;
      margin-top: 70px;
    }

    .hero h1 {
      font-size: 3rem;
      color: skyblue;
      margin-bottom: 15px;
      letter-spacing: 1px;
    }

    .hero p {
      font-size: 1.3rem;
      max-width: 800px;
      line-height: 1.7;
      margin-bottom: 30px;
    }

    .hero .btn {
      background: skyblue;
      color: #000;
      padding: 12px 25px;
      border-radius: 25px;
      border: none;
      font-size: 1.1rem;
      cursor: pointer;
      transition: 0.3s;
      text-decoration: none;
    }

    .hero .btn:hover {
      background: #fff;
      color: #000;
    }

    /* Footer */
    footer {
      text-align: center;
      background: rgba(0, 0, 0, 0.8);
      padding: 15px;
      font-size: 0.9rem;
    }

    /* Responsive tweaks */
    @media (max-width: 700px) {
      nav { padding: 12px 16px; }
      nav .logo { font-size: 1.4rem; }
      nav ul { gap: 12px; }
      .hero h1 { font-size: 2rem; }
      .hero p { font-size: 1rem; padding: 0 10px; }
    }
  </style>
</head>
<body>

  <nav>
    <div class="logo">🎬 Movie Poll</div>

    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>

      <?php if ($isLoggedIn): ?>
        <li class="nav-user" style="margin-left:20px;">
          <span>👤</span>
          <span class="name"><?= htmlspecialchars($fullname) ?></span>
        </li>
      <?php else: ?>
        <li style="margin-left:20px;"><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <section class="hero">
    <h1>Welcome to Movie Poll</h1>
    <p>
      Discover new films, share your favorite picks, and see which movies top the charts!
      Movie Poll lets you vote, explore, and connect with a community of movie lovers — all in one fun and interactive space.
    </p>
    <a href="dashboard.php" class="btn">Start Voting 🎥</a>
  </section>

  <footer>
    © <?php echo date("Y"); ?> Movie Poll — Where movie lovers come together.
  </footer>

</body>
</html>






