<?php
session_start();

include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get and normalize the form data
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;

    // Basic validation
    if (!empty($fullname) && !empty($username) && !empty($email) && !empty($password) && !empty($confirmPassword) && $terms) {
        if ($password === $confirmPassword) {
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email address.";
            } else {
                // Use prepared statements to avoid SQL injection
                $stmt = mysqli_prepare($connection, "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 0) {
                        mysqli_stmt_close($stmt);

                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                        $insertStmt = mysqli_prepare($connection, "INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
                        if ($insertStmt) {
                            mysqli_stmt_bind_param($insertStmt, 'ssss', $fullname, $username, $email, $hashedPassword);
                            $executed = mysqli_stmt_execute($insertStmt);
                            mysqli_stmt_close($insertStmt);

                            if ($executed) {
                                header("Location: login.php");
                                die;
                            } else {
                                echo "Error creating account. Please try again later.";
                            }
                        } else {
                            echo "Database error (insert).";
                        }
                    } else {
                        mysqli_stmt_close($stmt);
                        echo "Username or Email already exists.";
                    }
                } else {
                    echo "Database error (query).";
                }
            }
        } else {
            echo "Passwords do not match.";
        }
    } else {
        echo "Please fill in all fields and agree to the terms.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .signup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .signup-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .signup-header h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .signup-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .signup-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .signup-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .signup-button:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #999;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            padding: 0 15px;
        }

        .login-link {
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
            margin-top: 3px;
            width: 18px;
            height: 18px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .checkbox-group label {
            color: #666;
            font-size: 13px;
            cursor: pointer;
            margin: 0;
            line-height: 1.5;
        }

        .checkbox-group label a {
            color: #667eea;
            text-decoration: none;
        }

        .checkbox-group label a:hover {
            color: #764ba2;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 12px;
        }

        .strength-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { width: 33%; background: #ff4757; }
        .strength-medium { width: 66%; background: #ffa502; }
        .strength-strong { width: 100%; background: #26de81; }
    </style>
</head>
<body>
    <div class="signup-container">
        
        <form action="signup.php" method="POST">
            <div class="signup-header">
                <h1>Create Account</h1>
                <p>Sign up to get started</p>
            </div>

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" oninput="checkPasswordStrength()">
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password">
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms">
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="button" class="signup-button" onclick="handleSignup()">Sign Up</button>
        </form>

        <div class="divider">
            <span>OR</span>
        </div>

        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthFill = document.getElementById('strengthFill');
            
            strengthFill.className = 'strength-fill';
            
            if (password.length === 0) {
                strengthFill.style.width = '0%';
            } else if (password.length < 6) {
                strengthFill.classList.add('strength-weak');
            } else if (password.length < 10) {
                strengthFill.classList.add('strength-medium');
            } else {
                strengthFill.classList.add('strength-strong');
            }
        }

        function handleSignup() {
            const fullname = document.getElementById('fullname').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const terms = document.getElementById('terms').checked;

            if (!fullname || !username || !email || !password || !confirmPassword) {
                alert('Please fill in all fields');
                return;
            }

            if (!terms) {
                alert('Please agree to the Terms of Service and Privacy Policy');
                return;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters long');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }

            // Submit the validated form to the backend
            console.log('Signup attempt (submitting):', { fullname, username, email });
            document.querySelector('form').submit();
        }

        // Allow Enter key to submit
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSignup();
            }
        });
    </script>
</body>
</html>