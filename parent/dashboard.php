<?php
require_once '../config/config.php';

if (!isLoggedIn('parent')) {
    redirect('login.php');
}

$page_title = 'Dashboard';
$parent_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM child WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$total_children = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$total_bookings = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM request WHERE parent_id = ? AND request_status = 'Pending'");
$stmt->execute([$parent_id]);
$pending_requests = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE parent_id = ? AND status = 'Completed'");
$stmt->execute([$parent_id]);
$completed_vaccinations = $stmt->fetch()['total'];

// Get upcoming appointments
$stmt = $conn->prepare("
    SELECT b.*, c.child_name, v.vaccine_name, h.hospital_name, h.location
    FROM booking b
    JOIN child c ON b.child_id = c.child_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    JOIN hospital h ON b.hospital_id = h.hospital_id
    WHERE b.parent_id = ? AND b.booking_date >= CURDATE() AND b.status != 'Completed'
    ORDER BY b.booking_date ASC
    LIMIT 5
");
$stmt->execute([$parent_id]);
$upcoming_appointments = $stmt->fetchAll();

// Get children
$stmt = $conn->prepare("SELECT * FROM child WHERE parent_id = ? ORDER BY child_id DESC LIMIT 4");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="welcome-banner">
    <div class="welcome-content">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
        <p>Manage your children's vaccination schedules and appointments</p>
    </div>
    <div class="welcome-actions">
        <a href="appointments.php" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Book Appointment
        </a>
        <a href="children.php" class="btn btn-secondary">
            <i class="fas fa-plus"></i> Add Child
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid-dashboard">
    <div class="stat-card-dashboard">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-baby"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $total_children; ?></div>
            <div class="stat-label">My Children</div>
        </div>
    </div>
    
    <div class="stat-card-dashboard">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $total_bookings; ?></div>
            <div class="stat-label">Total Appointments</div>
        </div>
    </div>
    
    <div class="stat-card-dashboard">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $pending_requests; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
    </div>
    
    <div class="stat-card-dashboard">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $completed_vaccinations; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid">
    <!-- Upcoming Appointments -->
    <div class="dashboard-card">
        <div class="card-header-dashboard">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Appointments</h3>
            <a href="appointments.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-dashboard">
            <?php if (count($upcoming_appointments) > 0): ?>
                <div class="appointments-list">
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-date">
                                <div class="date-day"><?php echo date('d', strtotime($appointment['booking_date'])); ?></div>
                                <div class="date-month"><?php echo date('M', strtotime($appointment['booking_date'])); ?></div>
                            </div>
                            <div class="appointment-details-dash">
                                <h4><?php echo htmlspecialchars($appointment['child_name']); ?></h4>
                                <p><i class="fas fa-syringe"></i> <?php echo htmlspecialchars($appointment['vaccine_name']); ?></p>
                                <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($appointment['hospital_name']); ?></p>
                                <p><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-card">
                    <i class="fas fa-calendar-times"></i>
                    <p>No upcoming appointments</p>
                    <a href="appointments.php" class="btn btn-sm btn-primary">Book Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- My Children -->
    <div class="dashboard-card">
        <div class="card-header-dashboard">
            <h3><i class="fas fa-baby"></i> My Children</h3>
            <a href="children.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-dashboard">
            <?php if (count($children) > 0): ?>
                <div class="children-list">
                    <?php foreach ($children as $child): ?>
                        <div class="child-item">
                            <div class="child-avatar-dash">
                                <i class="fas fa-child"></i>
                            </div>
                            <div class="child-info-dash">
                                <h4><?php echo htmlspecialchars($child['child_name']); ?></h4>
                                <p><?php echo $child['gender']; ?> • <?php echo calculateAge($child['date_of_birth']); ?></p>
                                <span class="blood-badge"><?php echo $child['blood_group']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-card">
                    <i class="fas fa-baby"></i>
                    <p>No children added yet</p>
                    <a href="children.php" class="btn btn-sm btn-primary">Add Child</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
    <div class="actions-grid">
        <a href="appointments.php" class="action-card">
            <div class="action-icon" style="background: #dbeafe; color: #1e40af;">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h4>Book Appointment</h4>
            <p>Schedule a new vaccination</p>
        </a>
        
        <a href="children.php" class="action-card">
            <div class="action-icon" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h4>Add Child</h4>
            <p>Register a new child</p>
        </a>
        
        <a href="reports.php" class="action-card">
            <div class="action-icon" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-file-medical"></i>
            </div>
            <h4>View Reports</h4>
            <p>Check vaccination history</p>
        </a>
        
        <a href="profile.php" class="action-card">
            <div class="action-icon" style="background: #e0e7ff; color: #3730a3;">
                <i class="fas fa-user-edit"></i>
            </div>
            <h4>Edit Profile</h4>
            <p>Update your information</p>
        </a>
    </div>
</div>

<style>
.welcome-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.welcome-content h1 {
    font-size: 2.2rem;
    margin-bottom: 10px;
}

.welcome-content p {
    opacity: 0.95;
    font-size: 1.1rem;
}

.welcome-actions {
    display: flex;
    gap: 15px;
}

.welcome-actions .btn {
    background: white;
    color: #667eea;
}

.welcome-actions .btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.stats-grid-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-dashboard {
    background: white;
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.stat-card-dashboard:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-label {
    color: #64748b;
    font-size: 0.95rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.dashboard-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.card-header-dashboard {
    padding: 20px 25px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-dashboard h3 {
    color: #1e293b;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.view-all {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
}

.view-all:hover {
    gap: 10px;
}

.card-body-dashboard {
    padding: 25px;
}

.appointments-list, .children-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.appointment-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.appointment-item:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

.appointment-date {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.date-day {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}

.date-month {
    font-size: 0.85rem;
    opacity: 0.9;
}

.appointment-details-dash h4 {
    color: #1e293b;
    margin-bottom: 8px;
}

.appointment-details-dash p {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.appointment-details-dash i {
    color: #667eea;
    width: 15px;
}

.child-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.child-item:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

.child-avatar-dash {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.child-info-dash h4 {
    color: #1e293b;
    margin-bottom: 4px;
}

.child-info-dash p {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 6px;
}

.blood-badge {
    display: inline-block;
    padding: 3px 10px;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.empty-state-card {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}

.empty-state-card i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #cbd5e1;
}

.empty-state-card p {
    margin-bottom: 15px;
}

.quick-actions {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.quick-actions h3 {
    color: #1e293b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.action-card {
    padding: 25px;
    background: #f8fafc;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.action-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 15px;
}

.action-card h4 {
    color: #1e293b;
    margin-bottom: 8px;
}

.action-card p {
    color: #64748b;
    font-size: 0.9rem;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .welcome-banner {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .stats-grid-dashboard {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>