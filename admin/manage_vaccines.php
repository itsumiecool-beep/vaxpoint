<?php
session_start();
if (!isset($_SESSION['hospital_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

/* ================= ADD VACCINE ================= */
if (isset($_POST['add_vaccine'])) {
    $stmt = $conn->prepare(
        "INSERT INTO vaccine (vaccine_name, description, availability) VALUES (?, ?, 'Available')"
    );
    $stmt->execute([trim($_POST['name']), trim($_POST['description'])]);
    header("Location: manage_vaccines.php");
    exit;
}

/* ================= UPDATE VACCINE ================= */
if (isset($_POST['update_vaccine'])) {
    $stmt = $conn->prepare(
        "UPDATE vaccine SET vaccine_name = ?, description = ?, availability = ? WHERE vaccine_id = ?"
    );
    $stmt->execute([
        trim($_POST['name']),
        trim($_POST['description']),
        $_POST['status'],
        (int)$_POST['id']
    ]);
    header("Location: manage_vaccines.php");
    exit;
}

/* ================= FETCH ALL VACCINES ================= */
$vaccines = $conn->query(
    "SELECT vaccine_id, vaccine_name, description, availability FROM vaccine ORDER BY vaccine_id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

/* ================= FETCH VACCINE FOR EDIT ================= */
$editVaccine = null;
if (!empty($_GET['edit'])) {
    $stmt = $conn->prepare(
        "SELECT vaccine_id, vaccine_name, description, availability FROM vaccine WHERE vaccine_id = ?"
    );
    $stmt->execute([(int)$_GET['edit']]);
    $editVaccine = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hospital | Vaccine Management</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* ===== PAGE LAYOUT ===== */
body {
    background: #f6f9fc;
    font-family: system-ui, sans-serif;
}
.container {
    padding: 60px 5%;
    max-width: 1100px;
    margin: auto;
}

/* ===== PAGE TITLE ===== */
.page-title {
    text-align: center;
    font-size: 36px;
    color: #0a2540;
    margin-bottom: 40px;
}

/* ===== FORM ===== */
.form-card {
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    max-width: 600px;
    margin: 0 auto 50px auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
.form-card h2 {
    font-size: 24px;
    color: #0e3a5d;
    margin-bottom: 25px;
    text-align: center;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #0a2540;
}
.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
}
.form-group textarea {
    min-height: 100px;
}

/* ===== BUTTON ===== */
.btn {
    width: 100%;
    padding: 14px 0;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    font-size: 16px;
    background: linear-gradient(135deg, #0a2540, #1fc8b8);
    color: white;
    transition: 0.3s;
}
.btn:hover {
    opacity: 0.9;
}

/* ===== TABLE ===== */
.table-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}
.table-card table {
    width: 100%;
    border-collapse: collapse;
}
.table-card th,
.table-card td {
    padding: 14px 16px;
    text-align: left;
}
.table-card thead {
    background: #f1f5f9;
    color: #0a2540;
    font-weight: 600;
}
.table-card tbody tr:hover {
    background: #f1f5f9;
}

/* ===== STATUS BADGES ===== */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.status-badge.available {
    background: #ecfdf5;
    color: #047857;
}
.status-badge.unavailable {
    background: #fee2e2;
    color: #991b1b;
}

/* ===== ACTION LINKS ===== */
.actions a {
    color: #1fc8b8;
    font-weight: 600;
    text-decoration: none;
}
.actions a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
    <div class="page-title">💉 Vaccine Management</div>

    <!-- FORM -->
    <div class="form-card">
        <h2><?= $editVaccine ? "Edit Vaccine" : "Add New Vaccine"; ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $editVaccine['vaccine_id'] ?? '' ?>">
            <div class="form-group">
                <label>Vaccine Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($editVaccine['vaccine_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?= htmlspecialchars($editVaccine['description'] ?? '') ?></textarea>
            </div>
            <?php if ($editVaccine): ?>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Available" <?= $editVaccine['availability']=='Available'?'selected':'' ?>>Available</option>
                    <option value="Unavailable" <?= $editVaccine['availability']=='Unavailable'?'selected':'' ?>>Unavailable</option>
                </select>
            </div>
            <?php endif; ?>
            <button class="btn" name="<?= $editVaccine ? 'update_vaccine' : 'add_vaccine' ?>">
                <?= $editVaccine ? "Update Vaccine" : "Add Vaccine" ?>
            </button>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Vaccine</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($vaccines as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['vaccine_name']) ?></td>
                    <td><?= htmlspecialchars($v['description']) ?></td>
                    <td>
                        <span class="status-badge <?= strtolower($v['availability']) ?>">
                            <?= $v['availability'] ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="?edit=<?= $v['vaccine_id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
