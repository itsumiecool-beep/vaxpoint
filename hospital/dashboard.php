<?php
require_once '../config/config.php';

if (!isLoggedIn('hospital')) {
    redirect('login.php');
}

$page_title = 'Hospital Dashboard';
$hospital_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$total_appointments = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM request WHERE hospital_id = ? AND request_status = 'Pending'");
$stmt->execute([$hospital_id]);
$pending_requests = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE hospital_id = ? AND status = 'Completed'");
$stmt->execute([$hospital_id]);
$completed_vaccinations = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE hospital_id = ? AND booking_date >= CURDATE() AND status != 'Completed'");
$stmt->execute([$hospital_id]);
$upcoming_appointments = $stmt->fetch()['total'];

// Get today's appointments
$stmt = $conn->prepare("
    SELECT b.*, 
           c.child_name, c.gender, c.date_of_birth,
           v.vaccine_name,
           p.name as parent_name, p.phone as parent_phone
    FROM booking b
    JOIN child c ON b.child_id = c.child_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    JOIN parent p ON b.parent_id = p.parent_id
    WHERE b.hospital_id = ? AND b.booking_date = CURDATE() AND b.status != 'Completed'
    ORDER BY b.appointment_time ASC
");
$stmt->execute([$hospital_id]);
$today_appointments = $stmt->fetchAll();

// Get recent pending requests
$stmt = $conn->prepare("
    SELECT r.*, 
           c.child_name,
           v.vaccine_name,
           p.name as parent_name
    FROM request r
    JOIN child c ON r.child_id = c.child_id
    JOIN vaccine v ON r.vaccine_id = v.vaccine_id
    JOIN parent p ON r.parent_id = p.parent_id
    WHERE r.hospital_id = ? AND r.request_status = 'Pending'
    ORDER BY r.created_at DESC
    LIMIT 5
");
$stmt->execute([$hospital_id]);
$recent_requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="welcome-banner-hospital">
    <div class="welcome-content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['hospital_name']); ?>! 🏥</h1>
        <p>Manage vaccination appointments and requests</p>
    </div>
    <div class="welcome-actions">
        <a href="requests.php" class="btn btn-primary">
            <i class="fas fa-clipboard-list"></i> View Requests
        </a>
        <a href="appointments.php" class="btn btn-secondary">
            <i class="fas fa-calendar"></i> All Appointments
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid-hospital">
    <div class="stat-card-hospital">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $total_appointments; ?></div>
            <div class="stat-label">Total Appointments</div>
        </div>
    </div>
    
    <div class="stat-card-hospital">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $pending_requests; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
    </div>
    
    <div class="stat-card-hospital">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $upcoming_appointments; ?></div>
            <div class="stat-label">Upcoming</div>
        </div>
    </div>
    
    <div class="stat-card-hospital">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?php echo $completed_vaccinations; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid-hospital">
    <!-- Today's Appointments -->
    <div class="dashboard-card-hospital">
        <div class="card-header-hospital">
            <h3><i class="fas fa-calendar-day"></i> Today's Appointments</h3>
            <span class="count-badge"><?php echo count($today_appointments); ?></span>
        </div>
        <div class="card-body-hospital">
            <?php if (count($today_appointments) > 0): ?>
                <div class="today-list">
                    <?php foreach ($today_appointments as $appointment): ?>
                        <div class="today-item">
                            <div class="time-badge">
                                <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                            </div>
                            <div class="today-details">
                                <h4><?php echo htmlspecialchars($appointment['child_name']); ?></h4>
                                <p><i class="fas fa-syringe"></i> <?php echo htmlspecialchars($appointment['vaccine_name']); ?></p>
                                <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($appointment['parent_name']); ?></p>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['parent_phone']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-card">
                    <i class="fas fa-calendar-times"></i>
                    <p>No appointments scheduled for today</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pending Requests -->
    <div class="dashboard-card-hospital">
        <div class="card-header-hospital">
            <h3><i class="fas fa-clipboard-list"></i> Pending Requests</h3>
            <a href="requests.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-hospital">
            <?php if (count($recent_requests) > 0): ?>
                <div class="requests-list">
                    <?php foreach ($recent_requests as $request): ?>
                        <div class="request-item">
                            <div class="request-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="request-details">
                                <h4><?php echo htmlspecialchars($request['child_name']); ?></h4>
                                <p><i class="fas fa-syringe"></i> <?php echo htmlspecialchars($request['vaccine_name']); ?></p>
                                <p><i class="fas fa-calendar"></i> <?php echo formatDate($request['requested_date']); ?></p>
                            </div>
                            <a href="requests.php" class="btn btn-sm btn-primary">Review</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-card">
                    <i class="fas fa-inbox"></i>
                    <p>No pending requests</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions-hospital">
    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
    <div class="actions-grid-hospital">
        <a href="requests.php" class="action-card-hospital">
            <div class="action-icon" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h4>Review Requests</h4>
            <p>Approve or reject vaccination requests</p>
        </a>
        
        <a href="appointments.php" class="action-card-hospital">
            <div class="action-icon" style="background: #dbeafe; color: #1e40af;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h4>View Appointments</h4>
            <p>Check all scheduled appointments</p>
        </a>
        
        <a href="appointments.php" class="action-card-hospital">
            <div class="action-icon" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4>Complete Vaccination</h4>
            <p>Mark vaccinations as completed</p>
        </a>
        
        <a href="profile.php" class="action-card-hospital">
            <div class="action-icon" style="background: #e0e7ff; color: #3730a3;">
                <i class="fas fa-hospital-user"></i>
            </div>
            <h4>Hospital Profile</h4>
            <p>Update hospital information</p>
        </a>
    </div>
</div>

<style>
.welcome-banner-hospital {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
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
    color: #10b981;
}

.welcome-actions .btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.stats-grid-hospital {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-hospital {
    background: white;
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.stat-card-hospital:hover {
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

.dashboard-grid-hospital {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.dashboard-card-hospital {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.card-header-hospital {
    padding: 20px 25px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-hospital h3 {
    color: #1e293b;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.count-badge {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.view-all {
    color: #10b981;
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

.card-body-hospital {
    padding: 25px;
}

.today-list, .requests-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.today-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.today-item:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

.time-badge {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    text-align: center;
    flex-shrink: 0;
}

.today-details h4 {
    color: #1e293b;
    margin-bottom: 8px;
}

.today-details p {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.today-details i {
    color: #10b981;
    width: 15px;
}

.request-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.request-item:hover {
    background: #f1f5f9;
}

.request-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
}

.request-details {
    flex: 1;
}

.request-details h4 {
    color: #1e293b;
    margin-bottom: 5px;
}

.request-details p {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.request-details i {
    color: #10b981;
    width: 15px;
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

.quick-actions-hospital {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.quick-actions-hospital h3 {
    color: #1e293b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.actions-grid-hospital {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.action-card-hospital {
    padding: 25px;
    background: #f8fafc;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    text-align: center;
}

.action-card-hospital:hover {
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

.action-card-hospital h4 {
    color: #1e293b;
    margin-bottom: 8px;
}

.action-card-hospital p {
    color: #64748b;
    font-size: 0.9rem;
}

@media (max-width: 1024px) {
    .dashboard-grid-hospital {
        grid-template-columns: 1fr;
    }
    
    .welcome-banner-hospital {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .stats-grid-hospital {
        grid-template-columns: 1fr;
    }
    
    .actions-grid-hospital {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>