<?php
include("../config/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hospital_name = trim($_POST['hospital_name']);
    $address = trim($_POST['address']);
    $location = trim($_POST['location']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM hospital WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        $error = "Email already registered.";
    } else {
        $sql = "INSERT INTO hospital (hospital_name,address,location,email,password) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$hospital_name, $address, $location, $email, $password])) {
            $success = "Hospital registered successfully! You can now <a href='../auth/view_hospitals.php'>view</a>.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hospital Registration - VaxPoint</title>

<style>
  body, html {
    margin: 0; padding: 0;
    height: 100%;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0a2540, #1fc8b8);
    display: flex;
    justify-content: center;
    align-items: center;
    color: #e6f1ff;
  }

  .register-form {
    background: rgba(255, 255, 255, 0.1);
    padding: 30px 35px;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.5);
    width: 420px;
    max-width: 90vw;
  }

  h2 {
    margin-top: 0;
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: 700;
    color: #a8fff1;
    text-align: center;
  }

  label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 14px;
    color: #d0f0f6;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"],
  textarea {
    width: 100%;
    padding: 10px 14px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: #e6f1ff;
    font-size: 15px;
    outline: none;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    font-family: inherit;
    resize: vertical;
  }

  input:focus, textarea:focus {
    background: rgba(255, 255, 255, 0.35);
    box-shadow: 0 0 10px 3px rgba(34, 193, 195, 0.7);
  }

  textarea { min-height: 70px; }

  button {
    width: 100%;
    padding: 14px;
    background: #22c1c3;
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.3s ease, box-shadow 0.3s ease;
  }

  button:hover {
    background: #1fa9a9;
    box-shadow: 0 5px 15px rgba(31, 169, 169, 0.7);
  }

  .login-link {
    text-align: center;
    margin-top: 18px;
    font-size: 14px;
    color: #a8fff1;
  }

  .login-link a {
    color: #22c1c3;
    font-weight: 600;
    text-decoration: none;
  }

  .error-message, .success-message {
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
    font-size: 14px;
  }

  .error-message { background: rgba(255, 0, 0, 0.25); color: #ff4949; }
  .success-message { background: rgba(0, 255, 0, 0.25); color: #00ff99; }

  @media (max-width: 450px) {
    .register-form { width: 95vw; padding: 25px 20px; }
  }
</style>
</head>

<body>

<div class="register-form">
  <h2>Hospital Registration</h2>

  <?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success-message"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    
    <label for="hospital_name">Hospital Name</label>
    <input type="text" id="hospital_name" name="hospital_name" required />

    <label for="address">Address</label>
    <textarea id="address" name="address" required></textarea>

    <label for="location">Location</label>
    <input type="text" id="location" name="location" />

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required />

    <button type="submit">Register</button>
  </form>

  
</div>

</body>
</html>
