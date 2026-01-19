<?php
require_once '../config/config.php';

if (!isLoggedIn('parent')) {
    redirect('login.php');
}

$page_title = 'My Appointments';
$parent_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Handle new appointment request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
    $child_id = intval($_POST['child_id']);
    $vaccine_id = intval($_POST['vaccine_id']);
    $hospital_id = intval($_POST['hospital_id']);
    $requested_date = sanitize($_POST['requested_date']);
    $requested_time = sanitize($_POST['requested_time']);
    
    $stmt = $conn->prepare("INSERT INTO request (parent_id, child_id, vaccine_id, hospital_id, requested_date, requested_time, request_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    if ($stmt->execute([$parent_id, $child_id, $vaccine_id, $hospital_id, $requested_date, $requested_time])) {
        setFlashMessage('success', 'Appointment request submitted successfully! Waiting for admin approval.');
    } else {
        setFlashMessage('danger', 'Failed to submit request.');
    }
    redirect('appointments.php');
}

// Get children
$stmt = $conn->prepare("SELECT * FROM child WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll();

// Get vaccines
$stmt = $conn->query("SELECT * FROM vaccine WHERE availability = 'Available'");
$vaccines = $stmt->fetchAll();

// Get hospitals
$stmt = $conn->query("SELECT * FROM hospital WHERE status = 'Active'");
$hospitals = $stmt->fetchAll();

// Get all requests
$stmt = $conn->prepare("
    SELECT r.*, 
           c.child_name,
           v.vaccine_name,
           h.hospital_name, h.location
    FROM request r
    JOIN child c ON r.child_id = c.child_id
    JOIN vaccine v ON r.vaccine_id = v.vaccine_id
    JOIN hospital h ON r.hospital_id = h.hospital_id
    WHERE r.parent_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$parent_id]);
$requests = $stmt->fetchAll();

// Get approved bookings
$stmt = $conn->prepare("
    SELECT b.*, 
           c.child_name,
           v.vaccine_name,
           h.hospital_name, h.location, h.phone
    FROM booking b
    JOIN child c ON b.child_id = c.child_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    JOIN hospital h ON b.hospital_id = h.hospital_id
    WHERE b.parent_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->execute([$parent_id]);
$bookings = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-alt"></i> My Appointments</h1>
    <?php if (count($children) > 0): ?>
        <button class="btn btn-primary" onclick="openBookModal()">
            <i class="fas fa-plus"></i> Book Appointment
        </button>
    <?php endif; ?>
</div>

<?php
$flash = getFlashMessage();
if ($flash):
?>
    <div class="alert alert-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo $flash['message']; ?>
    </div>
<?php endif; ?>

<?php if (count($children) === 0): ?>
    <div class="empty-state">
        <i class="fas fa-baby"></i>
        <h3>No Children Added</h3>
        <p>Please add your child first before booking appointments</p>
        <a href="children.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Child
        </a>
    </div>
<?php else: ?>

<!-- Tabs -->
<div class="tabs">
    <button class="tab-btn active" onclick="showTab('pending')">
        <i class="fas fa-clock"></i> Pending Requests (<?php echo count(array_filter($requests, fn($r) => $r['request_status'] === 'Pending')); ?>)
    </button>
    <button class="tab-btn" onclick="showTab('approved')">
        <i class="fas fa-check-circle"></i> Approved Appointments (<?php echo count(array_filter($bookings, fn($b) => $b['status'] !== 'Completed')); ?>)
    </button>
    <button class="tab-btn" onclick="showTab('completed')">
        <i class="fas fa-clipboard-check"></i> Completed (<?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'Completed')); ?>)
    </button>
</div>

<!-- Pending Requests Tab -->
<div id="pending-tab" class="tab-content active">
    <?php 
    $pending = array_filter($requests, fn($r) => $r['request_status'] === 'Pending');
    if (count($pending) > 0): 
    ?>
        <div class="appointments-grid">
            <?php foreach ($pending as $request): ?>
                <div class="appointment-card pending">
                    <div class="appointment-header">
                        <span class="status-badge pending">
                            <i class="fas fa-clock"></i> Pending Approval
                        </span>
                        <span class="request-id">#<?php echo $request['request_id']; ?></span>
                    </div>
                    <div class="appointment-body">
                        <h3><?php echo htmlspecialchars($request['child_name']); ?></h3>
                        <div class="appointment-details">
                            <div class="detail">
                                <i class="fas fa-syringe"></i>
                                <span><?php echo htmlspecialchars($request['vaccine_name']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-hospital"></i>
                                <span><?php echo htmlspecialchars($request['hospital_name']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($request['location']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo formatDate($request['requested_date']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-clock"></i>
                                <span><?php echo date('h:i A', strtotime($request['requested_time'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state-small">
            <i class="fas fa-inbox"></i>
            <p>No pending requests</p>
        </div>
    <?php endif; ?>
</div>

<!-- Approved Appointments Tab -->
<div id="approved-tab" class="tab-content">
    <?php 
    $approved = array_filter($bookings, fn($b) => $b['status'] !== 'Completed');
    if (count($approved) > 0): 
    ?>
        <div class="appointments-grid">
            <?php foreach ($approved as $booking): ?>
                <div class="appointment-card approved">
                    <div class="appointment-header">
                        <span class="status-badge approved">
                            <i class="fas fa-check-circle"></i> Approved
                        </span>
                        <span class="booking-id">#<?php echo $booking['booking_id']; ?></span>
                    </div>
                    <div class="appointment-body">
                        <h3><?php echo htmlspecialchars($booking['child_name']); ?></h3>
                        <div class="appointment-details">
                            <div class="detail">
                                <i class="fas fa-syringe"></i>
                                <span><?php echo htmlspecialchars($booking['vaccine_name']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-hospital"></i>
                                <span><?php echo htmlspecialchars($booking['hospital_name']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($booking['location']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo formatDate($booking['booking_date']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-clock"></i>
                                <span><?php echo date('h:i A', strtotime($booking['appointment_time'])); ?></span>
                            </div>
                            <?php if ($booking['phone']): ?>
                            <div class="detail">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($booking['phone']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="appointment-footer">
                        <div class="reminder">
                            <i class="fas fa-bell"></i>
                            <span>Please visit the hospital on the scheduled date and time</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state-small">
            <i class="fas fa-calendar-check"></i>
            <p>No approved appointments</p>
        </div>
    <?php endif; ?>
</div>

<!-- Completed Tab -->
<div id="completed-tab" class="tab-content">
    <?php 
    $completed = array_filter($bookings, fn($b) => $b['status'] === 'Completed');
    if (count($completed) > 0): 
    ?>
        <div class="appointments-grid">
            <?php foreach ($completed as $booking): ?>
                <div class="appointment-card completed">
                    <div class="appointment-header">
                        <span class="status-badge completed">
                            <i class="fas fa-check-double"></i> Completed
                        </span>
                        <span class="booking-id">#<?php echo $booking['booking_id']; ?></span>
                    </div>
                    <div class="appointment-body">
                        <h3><?php echo htmlspecialchars($booking['child_name']); ?></h3>
                        <div class="appointment-details">
                            <div class="detail">
                                <i class="fas fa-syringe"></i>
                                <span><?php echo htmlspecialchars($booking['vaccine_name']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-hospital"></i>
                                <span><?php echo htmlspecialchars($booking['hospital_name']); ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo formatDate($booking['booking_date']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state-small">
            <i class="fas fa-clipboard-check"></i>
            <p>No completed vaccinations yet</p>
        </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<!-- Book Appointment Modal -->
<div id="bookModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-plus"></i> Book New Appointment</h3>
            <button class="close-modal" onclick="closeModal('bookModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="book">
            
            <div class="form-group">
                <label>Select Child *</label>
                <select name="child_id" class="form-control" required>
                    <option value="">Choose a child</option>
                    <?php foreach ($children as $child): ?>
                        <option value="<?php echo $child['child_id']; ?>">
                            <?php echo htmlspecialchars($child['child_name']); ?> (<?php echo calculateAge($child['date_of_birth']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Select Vaccine *</label>
                <select name="vaccine_id" class="form-control" required>
                    <option value="">Choose a vaccine</option>
                    <?php foreach ($vaccines as $vaccine): ?>
                        <option value="<?php echo $vaccine['vaccine_id']; ?>">
                            <?php echo htmlspecialchars($vaccine['vaccine_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Select Hospital *</label>
                <select name="hospital_id" class="form-control" required>
                    <option value="">Choose a hospital</option>
                    <?php foreach ($hospitals as $hospital): ?>
                        <option value="<?php echo $hospital['hospital_id']; ?>">
                            <?php echo htmlspecialchars($hospital['hospital_name']); ?> - <?php echo htmlspecialchars($hospital['location']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Preferred Date *</label>
                    <input type="date" name="requested_date" class="form-control" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Preferred Time *</label>
                    <input type="time" name="requested_time" class="form-control" required>
                </div>
            </div>
            
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <p>Your appointment request will be reviewed by the admin. You'll be notified once it's approved.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('bookModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #f1f5f9;
}

.tab-btn {
    padding: 15px 25px;
    border: none;
    background: none;
    color: #64748b;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tab-btn.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-btn:hover {
    color: #667eea;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.4s ease;
}

.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
}

.appointment-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 5px solid;
}

.appointment-card.pending {
    border-left-color: #f59e0b;
}

.appointment-card.approved {
    border-left-color: #10b981;
}

.appointment-card.completed {
    border-left-color: #3b82f6;
}

.appointment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.appointment-header {
    padding: 15px 20px;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.approved {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.completed {
    background: #dbeafe;
    color: #1e40af;
}

.request-id, .booking-id {
    font-weight: 600;
    color: #64748b;
}

.appointment-body {
    padding: 20px;
}

.appointment-body h3 {
    color: #1e293b;
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.appointment-details {
    display: grid;
    gap: 10px;
}

.detail {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748b;
    font-size: 0.9rem;
}

.detail i {
    color: #667eea;
    width: 20px;
}

.appointment-footer {
    padding: 15px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.reminder {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #10b981;
    font-size: 0.9rem;
}

.empty-state-small {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

.empty-state-small i {
    font-size: 4rem;
    margin-bottom: 15px;
    color: #cbd5e1;
}

.info-box {
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.info-box i {
    color: #3b82f6;
    font-size: 1.2rem;
}

.info-box p {
    color: #1e40af;
    font-size: 0.9rem;
    margin: 0;
}

@media (max-width: 768px) {
    .tabs {
        flex-direction: column;
    }
    
    .appointments-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function openBookModal() {
    document.getElementById('bookModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Set button active
    event.target.classList.add('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
</script>

<?php include 'includes/footer.php'; ?>