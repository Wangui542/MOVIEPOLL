<?php
session_start();
require_once __DIR__ . "/db_connect.php";

$message = "";
$email_value = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $email_value = $email;
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id, fullname FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        
        // Generate unique token
        $token = bin2hex(random_bytes(50));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires);
        
        if ($stmt->execute()) {
            // Create reset link
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/MOVIEPOLLPROJECT/reset_password.php?token=" . $token;
            $message = "<div class='success-message'>
                <h3>✅ Reset Link Generated!</h3>
                <p>Password reset link: <a href='$reset_link' class='reset-link'>Click here to reset your password</a></p>
                <p><small>This link expires in 1 hour.</small></p>
            </div>";
        } else {
            $message = "<div class='error-message'>Error generating reset link. Please try again.</div>";
        }
        
    } else {
        $message = "<div class='error-message'>Email not found in our system.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Movie Poll</title>
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

        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 12px;
            width: 450px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        h2 {
            color: skyblue;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            box-sizing: border-box;
            background: #333;
            color: white;
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

        .success-message {
            background: rgba(46, 125, 50, 0.3);
            border: 1px solid #2e7d32;
            color: #a5d6a7;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }

        .error-message {
            background: rgba(211, 47, 47, 0.3);
            border: 1px solid #d32f2f;
            color: #ef9a9a;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }

        .reset-link {
            color: skyblue;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
        }

        .reset-link:hover {
            background: rgba(255,255,255,0.2);
            text-decoration: none;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: skyblue;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Forgot Password</h2>
        
        <?php if (!empty($message)): ?>
            <?= $message ?>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Enter your email address:</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($email_value) ?>" 
                       placeholder="Your registered email" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>
        
        <div class="back-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>