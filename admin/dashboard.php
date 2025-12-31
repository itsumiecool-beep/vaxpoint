<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Access Denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | e-Vaccination</title>

    <!-- MAIN THEME -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* ================= ADMIN DASHBOARD ================= */

        .dashboard {
            padding: 70px 5%;
            position: relative;
            overflow: hidden;
        }

        .dashboard h2 {
            font-size: 34px;
            color: #0a2540;
            margin-bottom: 40px;
            animation: fadeDown 0.8s ease;
        }

        /* ===== DASH GRID ===== */
        .dash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        /* ===== DASH CARD ===== */
        .dash-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            position: relative;
            overflow: hidden;
            animation: floatUp 0.9s ease;
        }

        .dash-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #22c1c3, #1fc8b8);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .dash-card:hover::before {
            opacity: 0.08;
        }

        .dash-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .dash-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #0a2540;
            position: relative;
            z-index: 1;
        }

        .dash-card p {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 18px;
            position: relative;
            z-index: 1;
        }

        .dash-card a {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            background: linear-gradient(135deg, #22c1c3, #1fc8b8);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .dash-card a:hover {
            transform: scale(1.05);
        }

        /* ===== MOON ===== */
        .moon {
            position: absolute;
            right: -80px;
            top: 30px;
            width: 140px;
            height: 140px;
            background: #fff3c4;
            border-radius: 50%;
            box-shadow: -35px 0 0 #0e3a5d;
            animation: float 6s ease-in-out infinite;
            pointer-events: none;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        @keyframes floatUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-left">
        <span class="logo-text">e-Vaccination</span>
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="view_requests.php">Requests</a>
        <a href="view_hospitals.php">Hospitals</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- DASHBOARD -->
<section class="dashboard">
    <div class="moon"></div>

    <h2>Admin Dashboard</h2>

    <div class="dash-grid">

        <div class="dash-card">
            <h3>All Children</h3>
            <p>View registered children details.</p>
            <a href="reports.php">Open</a>
        </div>

        <div class="dash-card">
            <h3>Vaccination Schedule</h3>
            <p>Date & time of vaccinations.</p>
            <a href="reports.php">View</a>
        </div>

        <div class="dash-card">
            <h3>Vaccination Reports</h3>
            <p>Child & vaccine date-wise reports.</p>
            <a href="reports.php">Generate</a>
        </div>

        <div class="dash-card">
            <h3>Vaccine List</h3>
            <p>Available / Unavailable vaccines.</p>
            <a href="manage_vaccines.php">Manage</a>
        </div>

        <div class="dash-card">
            <h3>Parent Requests</h3>
            <p>Approve or reject booking requests.</p>
            <a href="view_requests.php">Review</a>
        </div>

        <div class="dash-card">
            <h3>Add Hospital</h3>
            <p>Register new hospitals.</p>
            <a href="add_hospital.php">Add</a>
        </div>

        <div class="dash-card">
            <h3>Manage Hospitals</h3>
            <p>Update or delete hospitals.</p>
            <a href="view_hospitals.php">Open</a>
        </div>

        <div class="dash-card">
            <h3>Booking Details</h3>
            <p>All vaccination bookings.</p>
            <a href="reports.php">View</a>
        </div>

    </div>
</section>

</body>
</html>
