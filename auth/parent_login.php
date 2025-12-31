<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

$error = ""; // variable to store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM parent WHERE email = ?");
    $stmt->execute([$email]);
    $parent = $stmt->fetch();

    if ($parent && password_verify($password, $parent['password'])) {
        $_SESSION['parent_id'] = $parent['parent_id'];
        header("Location: ../parent/dashboard.php");
        exit;
    } else {
        $error = "Invalid login details";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Parent Login - VaxPoint</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
      /* FULL HEIGHT BODY */
body, html {
    height: 100%;
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0a2540, #1fc8b8);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* LOGIN CONTAINER */
.login-container {
    background: rgba(255, 255, 255, 0.07);
    padding: 50px 40px;
    border-radius: 25px;
    width: 100%;
    max-width: 420px;
    backdrop-filter: blur(15px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
    animation: slideUp 0.8s ease forwards;
}

/* FUN BACKGROUND SHAPES */
.login-container::before {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle at top left, #22c1c3, #1fa9a9);
    border-radius: 50%;
    top: -60px;
    left: -60px;
    opacity: 0.6;
    z-index: 0;
}

.login-container::after {
    content: '';
    position: absolute;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle at bottom right, #a8fff1, #22c1c3);
    border-radius: 50%;
    bottom: -80px;
    right: -80px;
    opacity: 0.6;
    z-index: 0;
}

/* FORM CONTENT */
.login-container h2 {
    position: relative;
    z-index: 1;
    text-align: center;
    font-size: 36px;
    color: #fff;
    margin-bottom: 35px;
    letter-spacing: 1px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 15px;
    color: #e0f7fa;
    position: relative;
    z-index: 1;
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 14px 18px;
    margin-bottom: 25px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    outline: none;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

input[type="email"]:focus,
input[type="password"]:focus {
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 10px rgba(255,255,255,0.5);
}

button {
    width: 100%;
    background: linear-gradient(135deg, #22c1c3, #1fa9a9);
    border: none;
    padding: 16px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 18px;
    color: white;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(34, 193, 195, 0.5);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

button:hover {
    background: linear-gradient(135deg, #1fa9a9, #16a3a3);
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(34, 193, 195, 0.7);
}

/* REGISTER LINK */
.register-link {
    margin-top: 22px;
    text-align: center;
    font-size: 14px;
    color: #c0f0f5;
    position: relative;
    z-index: 1;
}

.register-link a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

.register-link a:hover {
    color: #22c1c3;
}

/* ERROR MESSAGE */
.error-message {
    background: rgba(255, 0, 0, 0.2);
    color: #ff4949;
    padding: 12px 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
    position: relative;
    z-index: 1;
}

/* ANIMATION */
@keyframes slideUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* RESPONSIVE */
@media (max-width: 500px) {
    .login-container {
        padding: 40px 25px;
    }
}

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Parent Login</h2>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email" />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" />

            <button type="submit">Login</button>
        </form>

        <p class="register-link">
            Don't have an account? <a href="../parent/register.php">Register here</a>
        </p>
    </div>
</body>
</html>
