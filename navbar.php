<?php
session_start();

// If a user is logged in, this will always be available everywhere
$loggedIn = isset($_SESSION['user_id']);
$fullname = $loggedIn ? $_SESSION['fullname'] : null;
?>
<nav>
  <h2>🎬 Movie Poll</h2>

  <ul>
    <li><a href="home.php">Home</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="results.php">Results</a></li> <!-- Added so user can see results easily -->
  </ul>

  <div class="user-info">
    <?php if($loggedIn): ?>
      <span>👤 <?= htmlspecialchars($fullname) ?></span>
      <a href="logout.php" class="logout-btn">Logout</a>
    <?php else: ?>
      <a href="login.php" class="logout-btn">Login</a>
    <?php endif; ?>
  </div>
</nav>

<style>
  nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: skyblue;
    padding: 15px 40px;
  }
  nav h2 { color: #000; margin: 0; }
  nav ul {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
  }
  nav ul li a {
    color: #000;
    text-decoration: none;
    font-weight: 500;
  }
  nav ul li a:hover { text-decoration: underline; }
  .user-info {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  .user-info span {
    color: #000;
    font-weight: 600;
  }
  .logout-btn {
    background: #000;
    color: #fff;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 20px;
    transition: background 0.3s ease;
  }
  .logout-btn:hover { background: #333; }
</style>

