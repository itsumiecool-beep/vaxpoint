<?php
session_start();
include("../config/db.php");

$hospital_id = $_SESSION['hospital_id'] ?? null;
if (!$hospital_id) {
    header("Location: login.php");
    exit();
}

// Handle update form submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaccine_id'], $_POST['availability'])) {
    $vaccine_id = $_POST['vaccine_id'];
    $availability = $_POST['availability'];

    $stmt_update = $conn->prepare("UPDATE hospital_vaccine SET availability_status=? WHERE hospital_id=? AND vaccine_id=?");
    $stmt_update->execute([$availability, $hospital_id, $vaccine_id]);

    $success = true;
}

// Ensure every vaccine has a row in hospital_vaccine
$vaccine_rows = $conn->query("SELECT vaccine_id FROM vaccine")->fetchAll(PDO::FETCH_ASSOC);
foreach ($vaccine_rows as $v) {
    $stmt_check = $conn->prepare("SELECT * FROM hospital_vaccine WHERE hospital_id=? AND vaccine_id=?");
    $stmt_check->execute([$hospital_id, $v['vaccine_id']]);
    if ($stmt_check->rowCount() == 0) {
        $stmt_insert = $conn->prepare("INSERT INTO hospital_vaccine (hospital_id, vaccine_id, availability_status) VALUES (?,?,?)");
        $stmt_insert->execute([$hospital_id, $v['vaccine_id'], 'Available']);
    }
}

// Fetch vaccines with their current status for this hospital
$stmt_vaccines = $conn->prepare("
    SELECT v.vaccine_id, v.vaccine_name, v.description, hv.availability_status AS availability
    FROM vaccine v
    LEFT JOIN hospital_vaccine hv
    ON v.vaccine_id = hv.vaccine_id AND hv.hospital_id = ?
");
$stmt_vaccines->execute([$hospital_id]);
$vaccines = $stmt_vaccines->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Vaccine Status | e-Vaccination</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
/* ===== GENERAL ===== */
body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: #f5f7fa;
    color: #0a2540;
}
a {
    text-decoration: none;
    color: inherit;
}

/* ===== NAVBAR ===== */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 5%;
   background: linear-gradient(135deg, #0a2540, #0e3a5d);
    color: #fff;
}
.nav-left .logo-text {
    font-weight: 700;
    font-size: 22px;
}
.nav-links a {
    margin-left: 20px;
    font-weight: 500;
    transition: 0.2s;
}
.nav-links a:hover {
    opacity: 0.8;
}

/* ===== DASHBOARD ===== */
.dashboard {
    padding: 60px 5%;
}
.dashboard h2 {
    font-size: 32px;
    margin-bottom: 30px;
    color: #0a2540;
}

/* ===== SUCCESS MESSAGE ===== */
.success-msg {
    margin-bottom: 20px;
    padding: 12px 18px;
    background: #ecfdf5;
    color: #047857;
    border-radius: 10px;
    font-weight: 600;
    max-width: 900px;
    margin-inline: auto;
}

/* ===== TABLE ===== */
.table-box {
    background: #fff;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    max-width: 1100px;
    margin: 0 auto;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-family: inherit;
}
table th, table td {
    padding: 12px 16px;
    text-align: left;
    font-size: 14px;
}
table th {
    background-color: #1fc8b8;
    color: #fff;
    font-weight: 600;
}
table td {
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

/* ===== STATUS BADGE ===== */
.status {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    text-align: center;
}
.Available {
    background: #d1fae5;
    color: #047857;
}
.Unavailable {
    background: #ffedd5;
    color: #c2410c;
}

/* ===== UPDATE FORM ===== */
.update-form {
    display: flex;
    align-items: center;
    gap: 8px;
}
.update-form select {
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-size: 13px;
}
.update-form button {
    padding: 6px 14px;
    border-radius: 8px;
    border: none;
    background: #22c1c3;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}
.update-form button:hover {
    background: #1fa9a9;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .nav-links a {
        margin-left: 10px;
        font-size: 14px;
    }
    table th, table td {
        padding: 8px 10px;
        font-size: 13px;
    }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-left"><span class="logo-text">e-Vaccination</span></div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="vaccine_status.php">Vaccine Status</a>
        <a href="bookings.php">Bookings</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<section class="dashboard">
    <h2>Update Vaccine Status</h2>

    <?php if ($success): ?>
        <div class="success-msg">
            Vaccine status updated successfully.
        </div>
    <?php endif; ?>

    <div class="table-box">
        <table>
            <tr>
                <th>Vaccine</th>
                <th>Description</th>
                <th>Status</th>
                <th>Update</th>
            </tr>

            <?php foreach ($vaccines as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <span class="status <?= $row['availability'] ?>">
                        <?= $row['availability'] ?>
                    </span>
                </td>
                <td>
                    <form class="update-form" method="POST" action="">
                        <input type="hidden" name="vaccine_id" value="<?= $row['vaccine_id'] ?>">
                        <select name="availability">
                            <option value="Available" <?= $row['availability'] === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Unavailable" <?= $row['availability'] === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

</body>
</html>
