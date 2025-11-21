<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "moviepoll_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Store session info
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['fullname'] = $row['fullname'] ?? '';
            $_SESSION['is_admin'] = isset($row['is_admin']) ? intval($row['is_admin']) : 0;

            // Redirect based on role
            if ($_SESSION['is_admin'] === 1) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Movie Poll</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: rgba(0, 0, 0, 0.8);
      padding: 40px;
      border-radius: 12px;
      width: 350px;
      text-align: center;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }

    h2 {
      color: skyblue;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      box-sizing: border-box;
    }

    button {
      width: 100%;
      background: skyblue;
      color: #000;
      padding: 12px;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      margin-top: 10px;
    }

    button:hover {
      background: deepskyblue;
    }

    p {
      margin-top: 15px;
    }

    a {
      color: skyblue;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    .error {
      background: rgba(255, 0, 0, 0.2);
      color: #ff6666;
      padding: 8px;
      border-radius: 8px;
      margin-bottom: 10px;
    }

    .links {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
    }

    .links a {
      color: skyblue;
      text-decoration: none;
      font-size: 14px;
    }

    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>🎬 Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <div class="links">
      <a href="forgot_password.php">Forgot password?</a>
      <a href="register.php">Create account</a>
    </div>
  </div>
</body>
</html>o9










