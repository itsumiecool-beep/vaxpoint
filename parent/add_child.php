<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['parent_id'])) {
    header("Location: ../auth/parent_login.php");
    exit;
}

$parent_id = $_SESSION['parent_id'];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['child_name']);
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = trim($_POST['blood_group']);

    if (empty($name) || empty($dob) || empty($gender)) {
        $error = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO child (parent_id, child_name, gender, date_of_birth, blood_group)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$parent_id, $name, $gender, $dob, $blood_group])) {
            $success = "Child added successfully!";
        } else {
            $error = "Failed to add child. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Child - VaxPoint</title>
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

/* FORM CONTAINER */
.add-child-container {
    background: rgba(255, 255, 255, 0.07);
    padding: 50px 40px;
    border-radius: 25px;
    width: 100%;
    max-width: 450px;
    backdrop-filter: blur(15px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
    animation: slideUp 0.8s ease forwards;
}

/* BACKGROUND SHAPES */
.add-child-container::before {
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

.add-child-container::after {
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
.add-child-container h2 {
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

input[type="text"],
input[type="date"],
select {
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

input[type="text"]:focus,
input[type="date"]:focus,
select:focus {
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

/* SUCCESS & ERROR MESSAGES */
.success-message, .error-message {
    text-align: center;
    padding: 12px 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.success-message { background: rgba(0, 255, 0, 0.2); color: #00ff6a; }
.error-message { background: rgba(255, 0, 0, 0.2); color: #ff4949; }

@keyframes slideUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}

@media (max-width: 500px) {
    .add-child-container { padding: 40px 25px; }
}
</style>
</head>
<body>

<div class="add-child-container">
    <h2>Add Child</h2>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="child_name">Child Name</label>
        <input type="text" id="child_name" name="child_name" required>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="date_of_birth">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required>

        <label for="blood_group">Blood Group</label>
        <input type="text" id="blood_group" name="blood_group" placeholder="Optional">

        <button type="submit">Add Child</button>
    </form>
</div>

</body>
</html>
