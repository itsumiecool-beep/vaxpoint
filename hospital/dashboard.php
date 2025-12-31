<?php
session_start();
if (!isset($_SESSION['hospital_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Dashboard | e-Vaccination</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- your theme css -->
    <style>
        /* ===== DASHBOARD ===== */
        .dashboard {
            padding: 60px 5%;
        }

        .dashboard h2 {
            font-size: 32px;
            margin-bottom: 30px;
            color: #0a2540;
        }

        .dash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .dash-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .dash-card:hover {
            transform: translateY(-6px);
        }

        .dash-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #0e3a5d;
        }

        .dash-card p {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .dash-card a {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 8px;
            background: #22c1c3;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .dash-card a:hover {
            background: #1fa9a9;
        }

        /* ===== TABLE ===== */
        .table-box {
            margin-top: 50px;
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            background: #f1f5f9;
            color: #0a2540;
            font-size: 14px;
        }

        table td {
            font-size: 14px;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status.pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status.completed {
            background: #ecfdf5;
            color: #047857;
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<div class="navbar">
    <div class="nav-left">
        <span class="logo-text">e-Vaccination</span>
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="update_vaccination_status.php">Vaccine Status</a>
        <a href="bookings.php">Bookings</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- ===== DASHBOARD ===== -->
<section class="dashboard">
    <h2>Hospital Dashboard</h2>

    <!-- CARDS -->
    <div class="dash-grid">
        <div class="dash-card">
            <h3>Update Vaccine Status</h3>
            <p>Add or update available / unavailable vaccines.</p>
            <a href="update_vaccination_status.php">Manage</a>
        </div>

        <div class="dash-card">
            <h3>Booking Requests</h3>
            <p>View parent booking requests for vaccination.</p>
            <a href="bookings.php">View</a>
        </div>

        <div class="dash-card">
            <h3>Vaccination Reports</h3>
            <p>View completed vaccination details.</p>
            <a href="reports.php">Open</a>
        </div>

        <div class="dash-card">
            <h3>My Profile</h3>
            <p>Update hospital information and contact details.</p>
            <a href="profile.php">Edit</a>
        </div>
    </div>

    <!-- TODAY BOOKINGS TABLE -->
    <div class="table-box">
        <h3>Today’s Vaccination Schedule</h3>
        <table>
            <tr>
                <th>Child Name</th>
                <th>Vaccine</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Rahul Kumar</td>
                <td>Polio</td>
                <td>2025-01-10</td>
                <td><span class="status pending">Pending</span></td>
            </tr>
            <tr>
                <td>Ananya Singh</td>
                <td>Hepatitis B</td>
                <td>2025-01-10</td>
                <td><span class="status completed">Completed</span></td>
            </tr>
        </table>
    </div>
</section>

</body>
</html>
