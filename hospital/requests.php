<?php
require_once '../config/config.php';

if (!isLoggedIn('hospital')) {
    redirect('login.php');
}

$page_title = 'Vaccination Requests';
$hospital_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Handle request action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        // Get request details
        $stmt = $conn->prepare("SELECT * FROM request WHERE request_id = ? AND hospital_id = ?");
        $stmt->execute([$request_id, $hospital_id]);
        $request = $stmt->fetch();
        
        if ($request) {
            // Update request status
            $stmt = $conn->prepare("UPDATE request SET request_status = 'Approved' WHERE request_id = ?");
            $stmt->execute([$request_id]);
            
            // Create booking
            $stmt = $conn->prepare("INSERT INTO booking (parent_id, child_id, hospital_id, vaccine_id, booking_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?, 'Approved')");
            $stmt->execute([
                $request['parent_id'],
                $request['child_id'],
                $request['hospital_id'],
                $request['vaccine_id'],
                $request['requested_date'],
                $request['requested_time']
            ]);
            
            setFlashMessage('success', 'Request approved successfully!');
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE request SET request_status = 'Rejected' WHERE request_id = ? AND hospital_id = ?");
        $stmt->execute([$request_id, $hospital_id]);
        setFlashMessage('success', 'Request rejected.');
    }
    
    redirect('requests.php');
}

// Get all requests for this hospital
$stmt = $conn->prepare("
    SELECT r.*, 
           p.name as parent_name, p.email as parent_email, p.phone as parent_phone,
           c.child_name, c.gender, c.date_of_birth, c.blood_group,
           v.vaccine_name
    FROM request r
    JOIN parent p ON r.parent_id = p.parent_id
    JOIN child c ON r.child_id = c.child_id
    JOIN vaccine v ON r.vaccine_id = v.vaccine_id
    WHERE r.hospital_id = ?
    ORDER BY 
        CASE WHEN r.request_status = 'Pending' THEN 1 ELSE 2 END,
        r.created_at DESC
");
$stmt->execute([$hospital_id]);
$requests = $stmt->fetchAll();

$pending_count = count(array_filter($requests, fn($r) => $r['request_status'] === 'Pending'));
$approved_count = count(array_filter($requests, fn($r) => $r['request_status'] === 'Approved'));
$rejected_count = count(array_filter($requests, fn($r) => $r['request_status'] === 'Rejected'));

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-clipboard-list"></i> Vaccination Requests</h1>
    <div class="header-stats">
        <span class="stat-badge pending"><?php echo $pending_count; ?> Pending</span>
        <span class="stat-badge approved"><?php echo $approved_count; ?> Approved</span>
        <span class="stat-badge rejected"><?php echo $rejected_count; ?> Rejected</span>
    </div>
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

<?php if (count($requests) > 0): ?>
    <div class="requests-grid">
        <?php foreach ($requests as $request): ?>
            <div class="request-card <?php echo strtolower($request['request_status']); ?>">
                <div class="request-header">
                    <span class="status-badge <?php echo strtolower($request['request_status']); ?>">
                        <?php if ($request['request_status'] === 'Pending'): ?>
                            <i class="fas fa-clock"></i> Pending
                        <?php elseif ($request['request_status'] === 'Approved'): ?>
                            <i class="fas fa-check-circle"></i> Approved
                        <?php else: ?>
                            <i class="fas fa-times-circle"></i> Rejected
                        <?php endif; ?>
                    </span>
                    <span class="request-id">#<?php echo $request['request_id']; ?></span>
                </div>
                
                <div class="request-body">
                    <div class="child-info-section">
                        <div class="child-avatar-req">
                            <i class="fas fa-child"></i>
                        </div>
                        <div>
                            <h3><?php echo htmlspecialchars($request['child_name']); ?></h3>
                            <div class="child-details-req">
                                <span><i class="fas fa-venus-mars"></i> <?php echo $request['gender']; ?></span>
                                <span><i class="fas fa-birthday-cake"></i> <?php echo calculateAge($request['date_of_birth']); ?></span>
                                <span><i class="fas fa-tint"></i> <?php echo $request['blood_group']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="request-details">
                        <div class="detail-row">
                            <i class="fas fa-syringe"></i>
                            <div>
                                <strong>Vaccine</strong>
                                <p><?php echo htmlspecialchars($request['vaccine_name']); ?></p>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <i class="fas fa-user"></i>
                            <div>
                                <strong>Parent</strong>
                                <p><?php echo htmlspecialchars($request['parent_name']); ?></p>
                                <p class="small"><?php echo htmlspecialchars($request['parent_email']); ?></p>
                                <p class="small"><?php echo htmlspecialchars($request['parent_phone']); ?></p>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Requested Date & Time</strong>
                                <p><?php echo formatDate($request['requested_date']); ?> at <?php echo date('h:i A', strtotime($request['requested_time'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($request['request_status'] === 'Pending'): ?>
                    <div class="request-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this request?')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <h3>No Requests Yet</h3>
        <p>Vaccination requests will appear here</p>
    </div>
<?php endif; ?>

<style>
.header-stats {
    display: flex;
    gap: 10px;
}

.stat-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.stat-badge.approved {
    background: #d1fae5;
    color: #065f46;
}

.stat-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.requests-grid {
    display: grid;
    gap: 25px;
}

.request-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border-left: 5px solid;
    transition: all 0.3s ease;
}

.request-card.pending {
    border-left-color: #f59e0b;
}

.request-card.approved {
    border-left-color: #10b981;
}

.request-card.rejected {
    border-left-color: #ef4444;
}

.request-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.request-header {
    padding: 20px;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
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

.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.request-id {
    font-weight: 600;
    color: #64748b;
}

.request-body {
    padding: 25px;
}

.child-info-section {
    display: flex;
    align-items: center;
    gap: 15px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f1f5f9;
    margin-bottom: 20px;
}

.child-avatar-req {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
}

.child-info-section h3 {
    color: #1e293b;
    margin-bottom: 8px;
}

.child-details-req {
    display: flex;
    gap: 15px;
    font-size: 0.85rem;
    color: #64748b;
}

.child-details-req span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.request-details {
    display: grid;
    gap: 15px;
}

.detail-row {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 10px;
}

.detail-row i {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.detail-row strong {
    display: block;
    color: #1e293b;
    margin-bottom: 4px;
}

.detail-row p {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

.detail-row p.small {
    font-size: 0.85rem;
    color: #94a3b8;
}

.request-actions {
    padding: 20px;
    background: #f8fafc;
    border-top: 2px solid #f1f5f9;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 5rem;
    margin-bottom: 20px;
    color: #cbd5e1;
}

.empty-state h3 {
    color: #475569;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .header-stats {
        flex-direction: column;
    }
    
    .child-info-section {
        flex-direction: column;
        text-align: center;
    }
    
    .child-details-req {
        flex-direction: column;
        gap: 8px;
    }
    
    .request-actions {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>