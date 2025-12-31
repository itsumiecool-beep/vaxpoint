<?php
session_start();
include("../config/db.php");

// Ensure parent is logged in
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch parent info
$parent_id = $_SESSION['parent_id'];
$parent = $conn->prepare("SELECT name FROM parent WHERE parent_id = ?");
$parent->execute([$parent_id]);
$parent = $parent->fetch(PDO::FETCH_ASSOC);

// Fetch children of the parent
$children = $conn->prepare("SELECT child_id, child_name FROM child WHERE parent_id = ?");
$children->execute([$parent_id]);
$children = $children->fetchAll(PDO::FETCH_ASSOC);

// Fetch vaccines
$vaccines = $conn->query("SELECT vaccine_id, vaccine_name FROM vaccine")->fetchAll(PDO::FETCH_ASSOC);

// Fetch hospitals
$hospitals = $conn->query("SELECT hospital_id, hospital_name FROM hospital")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_id = $_POST['child_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $hospital_id = $_POST['hospital_id'];
    $date = $_POST['requested_date'];
    $time = $_POST['requested_time']; // new field

    $stmt = $conn->prepare("INSERT INTO request (parent_id, child_id, vaccine_id, hospital_id, requested_date, requested_time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$parent_id, $child_id, $vaccine_id, $hospital_id, $date, $time]);

    $success = "Vaccination request submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Vaccination | e-Vaccination</title>
<style>
/* ===== PAGE BASE ===== */
body {
    margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background: #f6f9fc;
    color: #0f172a;
}
.container {
    max-width: 600px;
    margin: 80px auto;
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 25px;
    color: #0a2540;
}
form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #0e3a5d;
}
form input, form select {
    width: 100%;
    padding: 10px 14px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-size: 14px;
    outline: none;
}
form input:focus, form select:focus {
    border-color: #22c1c3;
    box-shadow: 0 0 0 3px rgba(34,193,195,0.25);
}
button {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background: #22c1c3;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}
button:hover { background: #1fa9a9; }
.success-message {
    background: #d1fae5;
    color: #047857;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
}
</style>
</head>
<body>
<div class="container">
<h2>Book Vaccination Appointment</h2>

<?php if ($success): ?>
<div class="success-message"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST">

    <!-- Parent Name (readonly) -->
    <label for="parent_name">Parent Name</label>
    <input type="text" id="parent_name" value="<?= htmlspecialchars($parent['name']) ?>" readonly>

    <!-- Child Name (select from parent's children) -->
    <label for="child_id">Child Name</label>
    <select id="child_id" name="child_id" required>
        <option value="">Select Child</option>
        <?php foreach ($children as $child): ?>
            <option value="<?= $child['child_id'] ?>"><?= htmlspecialchars($child['child_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Vaccine -->
    <label for="vaccine_id">Vaccine</label>
    <select id="vaccine_id" name="vaccine_id" required>
        <option value="">Select Vaccine</option>
        <?php foreach ($vaccines as $vaccine): ?>
            <option value="<?= $vaccine['vaccine_id'] ?>"><?= htmlspecialchars($vaccine['vaccine_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Hospital -->
    <label for="hospital_id">Hospital</label>
    <select id="hospital_id" name="hospital_id" required>
        <option value="">Select Hospital</option>
        <?php foreach ($hospitals as $hospital): ?>
            <option value="<?= $hospital['hospital_id'] ?>"><?= htmlspecialchars($hospital['hospital_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Appointment Date -->
    <label for="requested_date">Appointment Date</label>
    <input type="date" id="requested_date" name="requested_date" min="<?= date('Y-m-d') ?>" required>

    <!-- Appointment Time -->
    <label for="requested_time">Appointment Time</label>
    <input type="time" id="requested_time" name="requested_time" required>

    <button type="submit">Book Appointment</button>
</form>
</div>
</body>
</html>

