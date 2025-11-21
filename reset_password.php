<?php
session_start();
require_once __DIR__ . "/db_connect.php";

$message = "";
$valid_token = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT pr.user_id, u.email, pr.expires_at FROM password_resets pr 
                           JOIN users u ON pr.user_id = u.id 
                           WHERE pr.token = ? AND pr.used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $token_data = $result->fetch_assoc();
        
        // Check if token is expired
        if (strtotime($token_data['expires_at']) > time()) {
            $valid_token = true;
            $user_id = $token_data['user_id'];
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
                $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                // Update password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_password, $user_id);
                
                if ($stmt->execute()) {
                    // Mark token as used
                    $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    $stmt->execute();
                    
                    $message = "<div class='success-message'>✅ Password reset successfully! <a href='login.php'>Login with your new password</a></div>";
                    $valid_token = false;
                } else {
                    $message = "<div class='error-message'>❌ Error resetting password. Please try again.</div>";
                }
            }
        } else {
            $message = "<div class='error-message'>❌ Reset link has expired. Please request a new one.</div>";
        }
    } else {
        $message = "<div class='error-message'>❌ Invalid reset link. Please request a new one.</div>";
    }
} else {
    $message = "<div class='error-message'>❌ No reset token provided.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Movie Poll</title>
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
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
            font-weight: 500;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            box-sizing: border-box;
            background: #333;
            color: white;
            transition: all 0.3s ease;
        }

        input[type="password"]:focus {
            outline: none;
            background: #444;
            box-shadow: 0 0 0 2px skyblue;
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
            transition: background 0.3s ease;
        }

        button:hover {
            background: deepskyblue;
            transform: translateY(-2px);
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

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: skyblue;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: deepskyblue;
            text-decoration: underline;
        }

        .password-requirements {
            background: rgba(255,255,255,0.05);
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 14px;
            color: #ccc;
            text-align: left;
        }

        .password-requirements ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        .form-title {
            color: skyblue;
            margin-bottom: 25px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Reset Password</h2>
        
        <?php if (!empty($message)): ?>
            <?= $message ?>
        <?php endif; ?>

        <?php if ($valid_token): ?>
            <div class="form-title">Create your new password</div>
            
            <div class="password-requirements">
                <strong>Password Requirements:</strong>
                <ul>
                    <li>At least 6 characters long</li>
                    <li>Make it strong and unique</li>
                </ul>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Enter your new password" 
                           required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Re-enter your new password" 
                           required minlength="6">
                </div>
                
                <button type="submit" onclick="return validatePasswords()">
                    🔄 Reset Password
                </button>
            </form>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>

    <script>
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                alert('❌ Passwords do not match! Please make sure both passwords are identical.');
                return false;
            }
            
            if (password.length < 6) {
                alert('❌ Password must be at least 6 characters long!');
                return false;
            }
            
            return true;
        }

        // Real-time password matching indicator
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    this.style.boxShadow = '0 0 0 2px #4CAF50';
                } else {
                    this.style.boxShadow = '0 0 0 2px #f44336';
                }
            } else {
                this.style.boxShadow = '';
            }
        });
    </script>
</body>
</html>