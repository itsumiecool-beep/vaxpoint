<?php
session_start();
include("../config/db.php");

/* DELETE */
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM hospital WHERE hospital_id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header("Location: view_hospitals.php");
    exit;
}

/* UPDATE */
if (isset($_POST['update_hospital'])) {
    $stmt = $conn->prepare("
        UPDATE hospital 
        SET hospital_name=?, address=?, location=?, email=?
        WHERE hospital_id=?
    ");
    $stmt->execute([
        $_POST['hospital_name'],
        $_POST['address'],
        $_POST['location'],
        $_POST['email'],
        $_POST['hospital_id']
    ]);
    header("Location: view_hospitals.php");
    exit;
}

/* FETCH */
$hospitals = $conn->query("
    SELECT hospital_id, hospital_name, address, location, email
    FROM hospital
    ORDER BY hospital_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$editHospital = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM hospital WHERE hospital_id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editHospital = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hospital Management</title>

<style>
/* ===== GLOBAL ===== */
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background: #f6f9fc;
    color: #0f172a;
}

/* ===== NAVBAR ===== */
.navbar {
    height: 80px;
    padding: 0 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #0a2540, #0e3a5d);
}

.logo {
    font-size: 24px;
    font-weight: 600;
    color: #22c1c3;
}

/* ===== PAGE ===== */
.container {
    max-width: 1200px;
    margin: 60px auto;
    padding: 0 5%;
}

.page-title {
    font-size: 32px;
    margin-bottom: 30px;
    text-align: center;
}


/* ===== FORM ===== */
.form-box {
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    max-width: 520px;
    margin-bottom: 50px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

.form-box h2 {
    margin-bottom: 20px;
}

label {
    font-size: 13px;
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
}

input, textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 16px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-size: 14px;
}

textarea { min-height: 80px; }

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #22c1c3;
    color: #0a2540;
    font-weight: 700;
    cursor: pointer;
}

/* ===== TABLE ===== */
.table-box {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
     background-color: #1fc8b8;
    color: #e6f1ff;
    font-size: 14px;
    font-weight: 600;
    padding: 16px;
    text-align: center;
    border-bottom: none;
}


td {
    padding: 16px;
    border-top: 1px solid #e5e7eb;
    font-size: 14px;
}

.actions a {
    margin-right: 14px;
    font-weight: 600;
    text-decoration: none;
}

.actions .edit { color: #22c1c3; }
.actions .delete { color: #ef4444; }
</style>
</head>

<body>

<!-- NAV -->
<div class="navbar">
    <div class="logo">HealthAdmin</div>
</div>

<div class="container">
    <div class="page-title">🏥 Hospital Management</div>

    <!-- EDIT FORM -->
    <?php if ($editHospital): ?>
    <div class="form-box">
        <h2>Edit Hospital</h2>
        <form method="POST">
            <input type="hidden" name="hospital_id" value="<?= $editHospital['hospital_id'] ?>">

            <label>Hospital Name</label>
            <input name="hospital_name" required value="<?= htmlspecialchars($editHospital['hospital_name']) ?>">

            <label>Address</label>
            <textarea name="address" required><?= htmlspecialchars($editHospital['address']) ?></textarea>

            <label>Location</label>
            <input name="location" value="<?= htmlspecialchars($editHospital['location']) ?>">

            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($editHospital['email']) ?>">

            <button name="update_hospital">Update Hospital</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Location</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($hospitals as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['hospital_name']) ?></td>
                    <td><?= htmlspecialchars($h['address']) ?></td>
                    <td><?= htmlspecialchars($h['location']) ?></td>
                    <td><?= htmlspecialchars($h['email']) ?></td>
                    <td class="actions">
                        <a class="edit" href="?edit=<?= $h['hospital_id'] ?>">Edit</a>
                        <a class="delete" href="?delete=<?= $h['hospital_id'] ?>"
                           onclick="return confirm('Delete hospital?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

