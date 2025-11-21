<?php
session_start();
$conn = new mysqli("localhost", "root", "", "moviepoll_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = "";

// Handle register form
if (isset($_POST['register'])) {
    $fullname = trim($conn->real_escape_string($_POST['fullname']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);
            $stmt->execute();

            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['fullname'] = $fullname;
            header("Location: dashboard.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Movie Poll</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #0d0d0d;
      color: #fff;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .register-container {
      background: #1e1e1e;
      padding: 40px;
      border-radius: 15px;
      width: 380px;
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
      text-align: center;
    }
    .register-container h2 {
      color: skyblue;
      margin-bottom: 25px;
    }
    .register-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 25px;
      border: none;
      font-size: 16px;
    }
    .register-container button {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      border-radius: 25px;
      border: none;
      background: skyblue;
      color: #000;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .register-container button:hover {
      background: deepskyblue;
    }
    .register-container .error {
      background: #ff4d4d;
      color: #fff;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .register-container p {
      margin-top: 15px;
      font-size: 14px;
    }
    .register-container p a {
      color: skyblue;
      text-decoration: none;
    }
    .register-container p a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>🎬 Register</h2>

    <?php if(!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <input type="text" name="fullname" placeholder="Full Name" required value="">
      <input type="email" name="email" placeholder="Email" required value="">
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>


