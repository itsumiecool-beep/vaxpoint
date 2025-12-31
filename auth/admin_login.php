<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // --- Only allowed admin account ---
    $allowed_email = "itsissa900@gmail.com";
    $allowed_password = "kkmdmkah";

    // If matches hard-coded admin, login directly
    if ($email === $allowed_email && $password === $allowed_password) {

        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_email'] = $email;

        header("Location: ../admin/dashboard.php");
        exit;
    }

    // ---- (Optional) DB fallback login ----
    $stmt = $conn->prepare("SELECT admin_id, password FROM admin WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {

        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_email'] = $email;

        header("Location: ../admin/dashboard.php");
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
<title>Admin Login — VaxPoint</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

<style>
body, html {
    height: 100%;
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0a2540, #1fc8b8);
    display: flex;
    justify-content: center;
    align-items: center;
}

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

.login-container::before,
.login-container::after {
    pointer-events: none;
}

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
}

.login-container h2 {
    text-align: center;
    font-size: 36px;
    color: #fff;
    margin-bottom: 35px;
    letter-spacing: 1px;
    position: relative;
    z-index: 1;
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
    background: rgba(255,255,255,0.15);
    color: #fff;
    outline: none;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

input:focus {
    background: rgba(255,255,255,0.3);
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
    box-shadow: 0 10px 20px rgba(34,193,195,0.5);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

button:hover {
    background: linear-gradient(135deg, #1fa9a9, #16a3a3);
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(34,193,195,0.7);
}

.error-message {
    background: rgba(255,0,0,0.2);
    color: #ff4949;
    padding: 12px 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
    position: relative;
    z-index: 1;
}

.register-link {
    margin-top: 22px;
    text-align: center;
    font-size: 14px;
    color: #c0f0f5;
}

.register-link a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
}

.register-link a:hover {
    color: #22c1c3;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 500px) {
    .login-container { padding: 40px 25px; }
}
</style>
</head>

<body>

<div class="login-container">
    <h2>Admin Login</h2>

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

    <!-- Optional: Admin should not register publicly -->
    <!-- Replace or remove this if you want -->
    <p class="register-link">
        Admin access is restricted
    </p>
</div>

</body>
</html>
