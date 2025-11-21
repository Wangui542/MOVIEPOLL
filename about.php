<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About | Movie Poll</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: url('images/movie-bg.jpg') no-repeat center center/cover;
      color: white;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navigation bar */
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

    /* User info */
    .user-info span {
      font-weight: bold;
      color: skyblue;
    }

    /* About section */
    .about-section {
      margin-top: 120px;
      padding: 40px;
      max-width: 900px;
      background: rgba(0, 0, 0, 0.6);
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
      align-self: center;
    }

    .about-section h1 {
      font-size: 2.5rem;
      color: skyblue;
      margin-bottom: 20px;
      text-align: center;
    }

    .about-section p {
      font-size: 1.2rem;
      line-height: 1.8;
      text-align: center;
    }

    footer {
      margin-top: auto;
      text-align: center;
      padding: 20px;
      background: rgba(0, 0, 0, 0.8);
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

  <nav>
    <div class="logo">🎬 Movie Poll</div>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="about.php">About</a></li>

      <?php if (isset($_SESSION['fullname'])): ?>
          <li><a href="dashboard.php">Dashboard</a></li>
      <?php else: ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
      <?php endif; ?>
    </ul>

    <div class="user-info">
      <?php if(isset($_SESSION['fullname'])): ?>
        <span>👤 <?= htmlspecialchars($_SESSION['fullname']) ?></span>
      <?php endif; ?>
    </div>
  </nav>

  <section class="about-section">
    <h1>About Movie Poll</h1>
    <p>
      Movie Poll is where film lovers unite —  
      a place to celebrate the stories that move us, the heroes we admire,  
      and the unforgettable moments that stay with us long after the credits roll.
      <br><br>

      Here, every vote is a spotlight.  
      Simple, fun, and powered by the magic of cinema.
      <br><br>

      Pick. Vote. Discover.  
      The world of movies is waiting.
    </p>
  </section>

  <footer>
    © <?php echo date("Y"); ?> Movie Poll — Made with ❤️ for movie lovers.
  </footer>

</body>
</html>








