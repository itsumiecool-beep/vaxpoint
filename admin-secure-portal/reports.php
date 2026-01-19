<?php
require_once '../config/config.php';

$page_title = 'Reports & Analytics';

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$stats = [];

// Total vaccinations completed
$stmt = $conn->query("SELECT COUNT(*) as total FROM booking WHERE status = 'Completed'");
$stats['completed_vaccinations'] = $stmt->fetch()['total'];

// Vaccinations by vaccine type
$stmt = $conn->query("
    SELECT v.vaccine_name, COUNT(b.booking_id) as count
    FROM vaccine v
    LEFT JOIN booking b ON v.vaccine_id = b.vaccine_id
    GROUP BY v.vaccine_id
    ORDER BY count DESC
");
$vaccine_stats = $stmt->fetchAll();

// Monthly vaccination trend
$stmt = $conn->query("
    SELECT DATE_FORMAT(booking_date, '%Y-%m') as month, COUNT(*) as count
    FROM booking
    WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month
");
$monthly_stats = $stmt->fetchAll();

// Hospital performance
$stmt = $conn->query("
    SELECT h.hospital_name, COUNT(b.booking_id) as total_bookings
    FROM hospital h
    LEFT JOIN booking b ON h.hospital_id = b.hospital_id
    GROUP BY h.hospital_id
    ORDER BY total_bookings DESC
    LIMIT 10
");
$hospital_stats = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-value"><?php echo $stats['completed_vaccinations']; ?></div>
                <div class="stat-label">Completed Vaccinations</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Vaccinations by Vaccine Type</h3>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Vaccine Name</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vaccine_stats as $stat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($stat['vaccine_name']); ?></strong></td>
                        <td><span class="badge badge-info"><?php echo $stat['count']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hospital Performance</h3>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Hospital Name</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hospital_stats as $stat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($stat['hospital_name']); ?></strong></td>
                        <td><span class="badge badge-success"><?php echo $stat['total_bookings']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Monthly Vaccination Trend (Last 6 Months)</h3>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Vaccinations</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthly_stats as $stat): ?>
                    <tr>
                        <td><strong><?php echo date('F Y', strtotime($stat['month'] . '-01')); ?></strong></td>
                        <td><span class="badge badge-info"><?php echo $stat['count']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>