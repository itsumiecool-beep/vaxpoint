<?php
require_once '../config/config.php';

if (!isLoggedIn('hospital')) {
    redirect('login.php');
}

$page_title = 'Appointments';
$hospital_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Handle complete vaccination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete') {
    $booking_id = intval($_POST['booking_id']);
    $remarks = sanitize($_POST['remarks'] ?? '');
    
    // Update booking status
    $stmt = $conn->prepare("UPDATE booking SET status = 'Completed' WHERE booking_id = ? AND hospital_id = ?");
    if ($stmt->execute([$booking_id, $hospital_id])) {
        // Create vaccination report
        $stmt = $conn->prepare("INSERT INTO vaccination_report (booking_id, vaccination_date, status, remarks) VALUES (?, CURDATE(), 'Completed', ?)");
        $stmt->execute([$booking_id, $remarks]);
        
        setFlashMessage('success', 'Vaccination marked as completed!');
    } else {
        setFlashMessage('danger', 'Failed to update vaccination status.');
    }
    
    redirect('appointments.php');
}

// Get all appointments
$stmt = $conn->prepare("
    SELECT b.*, 
           p.name as parent_name, p.email as parent_email, p.phone as parent_phone,
           c.child_name, c.gender, c.date_of_birth, c.blood_group,
           v.vaccine_name
    FROM booking b
    JOIN parent p ON b.parent_id = p.parent_id
    JOIN child c ON b.child_id = c.child_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    WHERE b.hospital_id = ?
    ORDER BY b.booking_date DESC, b.appointment_time DESC
");
$stmt->execute([$hospital_id]);
$appointments = $stmt->fetchAll();

$upcoming = array_filter($appointments, fn($a) => strtotime($a['booking_date']) >= strtotime('today') && $a['status'] !== 'Completed');
$completed = array_filter($appointments, fn($a) => $a['status'] === 'Completed');

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-check"></i> Appointments</h1>
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

<!-- Tabs -->
<div class="tabs">
    <button class="tab-btn active" onclick="showTab('upcoming')">
        <i class="fas fa-calendar-alt"></i> Upcoming (<?php echo count($upcoming); ?>)
    </button>
    <button class="tab-btn" onclick="showTab('completed')">
        <i class="fas fa-check-double"></i> Completed (<?php echo count($completed); ?>)
    </button>
</div>

<!-- Upcoming Tab -->
<div id="upcoming-tab" class="tab-content active">
    <?php if (count($upcoming) > 0): ?>
        <div class="appointments-grid">
            <?php foreach ($upcoming as $appointment): ?>
                <div class="appointment-card">
                    <div class="appointment-date-badge">
                        <div class="date-day"><?php echo date('d', strtotime($appointment['booking_date'])); ?></div>
                        <div class="date-month"><?php echo date('M', strtotime($appointment['booking_date'])); ?></div>
                        <div class="date-time"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></div>
                    </div>
                    
                    <div class="appointment-content">
                        <div class="child-section-app">
                            <div class="child-avatar-app">
                                <i class="fas fa-child"></i>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars($appointment['child_name']); ?></h3>
                                <div class="child-meta-app">
                                    <span><?php echo $appointment['gender']; ?></span>
                                    <span><?php echo calculateAge($appointment['date_of_birth']); ?></span>
                                    <span><?php echo $appointment['blood_group']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="appointment-info">
                            <div class="info-item">
                                <i class="fas fa-syringe"></i>
                                <span><?php echo htmlspecialchars($appointment['vaccine_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($appointment['parent_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($appointment['parent_phone']); ?></span>
                            </div>
                        </div>
                        
                        <button class="btn btn-success btn-sm" onclick="openCompleteModal(<?php echo $appointment['booking_id']; ?>, '<?php echo htmlspecialchars($appointment['child_name']); ?>')">
                            <i class="fas fa-check-circle"></i> Mark as Completed
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state-small">
            <i class="fas fa-calendar-times"></i>
            <p>No upcoming appointments</p>
        </div>
    <?php endif; ?>
</div>

<!-- Completed Tab -->
<div id="completed-tab" class="tab-content">
    <?php if (count($completed) > 0): ?>
        <div class="appointments-grid">
            <?php foreach ($completed as $appointment): ?>
                <div class="appointment-card completed">
                    <div class="appointment-date-badge completed">
                        <div class="date-day"><?php echo date('d', strtotime($appointment['booking_date'])); ?></div>
                        <div class="date-month"><?php echo date('M', strtotime($appointment['booking_date'])); ?></div>
                        <div class="completed-badge">
                            <i class="fas fa-check"></i> Done
                        </div>
                    </div>
                    
                    <div class="appointment-content">
                        <div class="child-section-app">
                            <div class="child-avatar-app">
                                <i class="fas fa-child"></i>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars($appointment['child_name']); ?></h3>
                                <div class="child-meta-app">
                                    <span><?php echo $appointment['gender']; ?></span>
                                    <span><?php echo calculateAge($appointment['date_of_birth']); ?></span>
                                    <span><?php echo $appointment['blood_group']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="appointment-info">
                            <div class="info-item">
                                <i class="fas fa-syringe"></i>
                                <span><?php echo htmlspecialchars($appointment['vaccine_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($appointment['parent_name']); ?></span>
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

<!-- Complete Modal -->
<div id="completeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Complete Vaccination</h3>
            <button class="close-modal" onclick="closeModal('completeModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="complete">
            <input type="hidden" name="booking_id" id="complete_booking_id">
            
            <div class="complete-info">
                <i class="fas fa-info-circle"></i>
                <p>You are about to mark the vaccination for <strong id="complete_child_name"></strong> as completed.</p>
            </div>
            
            <div class="form-group">
                <label>Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" rows="3" placeholder="Any additional notes about the vaccination..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('completeModal')">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Mark as Completed
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
    color: #10b981;
    border-bottom-color: #10b981;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
}

.appointment-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    display: flex;
    gap: 20px;
    padding: 20px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.appointment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    border-color: #10b981;
}

.appointment-card.completed {
    opacity: 0.8;
}

.appointment-date-badge {
    width: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    color: white;
    flex-shrink: 0;
}

.appointment-date-badge.completed {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.date-day {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.date-month {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 10px;
}

.date-time {
    font-size: 0.85rem;
    opacity: 0.9;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.3);
}

.completed-badge {
    font-size: 0.8rem;
    padding: 5px;
    background: rgba(255,255,255,0.2);
    border-radius: 5px;
    margin-top: 10px;
}

.appointment-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.child-section-app {
    display: flex;
    align-items: center;
    gap: 12px;
}

.child-avatar-app {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.child-section-app h3 {
    color: #1e293b;
    margin-bottom: 5px;
}

.child-meta-app {
    display: flex;
    gap: 10px;
    font-size: 0.85rem;
    color: #64748b;
}

.appointment-info {
    display: grid;
    gap: 8px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748b;
    font-size: 0.9rem;
}

.info-item i {
    color: #10b981;
    width: 20px;
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

.complete-info {
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.complete-info i {
    color: #3b82f6;
    font-size: 1.2rem;
}

.complete-info p {
    color: #1e40af;
    font-size: 0.95rem;
    margin: 0;
}

@media (max-width: 768px) {
    .appointments-grid {
        grid-template-columns: 1fr;
    }
    
    .appointment-card {
        flex-direction: column;
    }
    
    .appointment-date-badge {
        width: 100%;
    }
}
</style>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

function openCompleteModal(bookingId, childName) {
    document.getElementById('complete_booking_id').value = bookingId;
    document.getElementById('complete_child_name').textContent = childName;
    document.getElementById('completeModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
</script>

<?php include 'includes/footer.php'; ?>