<?php
require_once '../config/config.php';

if (!isLoggedIn('parent')) {
    redirect('login.php');
}

$page_title = 'My Profile';
$parent_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $name = sanitize($_POST['name']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address']);
            
            $stmt = $conn->prepare("UPDATE parent SET name = ?, phone = ?, address = ? WHERE parent_id = ?");
            if ($stmt->execute([$name, $phone, $address, $parent_id])) {
                $_SESSION['name'] = $name;
                setFlashMessage('success', 'Profile updated successfully!');
            } else {
                setFlashMessage('danger', 'Failed to update profile.');
            }
            redirect('profile.php');
        } elseif ($_POST['action'] === 'change_password') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Get current password
            $stmt = $conn->prepare("SELECT password FROM parent WHERE parent_id = ?");
            $stmt->execute([$parent_id]);
            $parent = $stmt->fetch();
            
            if (password_verify($current_password, $parent['password'])) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE parent SET password = ? WHERE parent_id = ?");
                    if ($stmt->execute([$hashed_password, $parent_id])) {
                        setFlashMessage('success', 'Password changed successfully!');
                    } else {
                        setFlashMessage('danger', 'Failed to change password.');
                    }
                } else {
                    setFlashMessage('danger', 'New passwords do not match.');
                }
            } else {
                setFlashMessage('danger', 'Current password is incorrect.');
            }
            redirect('profile.php');
        }
    }
}

// Get parent details
$stmt = $conn->prepare("SELECT * FROM parent WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$parent = $stmt->fetch();

// Get statistics
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM child WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$total_children = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$total_bookings = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking WHERE parent_id = ? AND status = 'Completed'");
$stmt->execute([$parent_id]);
$completed_vaccinations = $stmt->fetch()['total'];

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
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

<div class="profile-container">
    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-avatar-large">
            <i class="fas fa-user"></i>
        </div>
        <h2><?php echo htmlspecialchars($parent['name']); ?></h2>
        <p class="profile-email"><?php echo htmlspecialchars($parent['email']); ?></p>
        <p class="profile-joined">Member since <?php echo formatDate($parent['created_at']); ?></p>
        
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-value"><?php echo $total_children; ?></div>
                <div class="stat-label">Children</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Appointments</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $completed_vaccinations; ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>
    
    <!-- Profile Details -->
    <div class="profile-details">
        <!-- Personal Information -->
        <div class="details-section">
            <div class="section-header">
                <h3><i class="fas fa-user-edit"></i> Personal Information</h3>
                <button class="btn btn-primary btn-sm" onclick="openEditModal()">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <value><?php echo htmlspecialchars($parent['name']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <value><?php echo htmlspecialchars($parent['email']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <value><?php echo htmlspecialchars($parent['phone']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-map-marker-alt"></i> Address</label>
                    <value><?php echo htmlspecialchars($parent['address']); ?></value>
                </div>
            </div>
        </div>
        
        <!-- Security -->
        <div class="details-section">
            <div class="section-header">
                <h3><i class="fas fa-lock"></i> Security</h3>
                <button class="btn btn-primary btn-sm" onclick="openPasswordModal()">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </div>
            <div class="security-info">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <strong>Password</strong>
                    <p>Last changed: Recently</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Edit Profile</h3>
            <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($parent['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number *</label>
                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($parent['phone']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Address *</label>
                <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($parent['address']); ?></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Change Password</h3>
            <button class="close-modal" onclick="closeModal('passwordModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label>Current Password *</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>New Password *</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
                <small>Must be at least 8 characters with uppercase, lowercase, and numbers</small>
            </div>
            
            <div class="form-group">
                <label>Confirm New Password *</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('passwordModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.profile-container {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 30px;
}

.profile-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    color: white;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.profile-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 4rem;
    border: 5px solid rgba(255,255,255,0.3);
}

.profile-card h2 {
    font-size: 1.8rem;
    margin-bottom: 8px;
}

.profile-email {
    opacity: 0.9;
    margin-bottom: 5px;
}

.profile-joined {
    opacity: 0.8;
    font-size: 0.9rem;
    margin-bottom: 30px;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
}

.profile-details {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.details-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.section-header h3 {
    color: #1e293b;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item label {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-item value {
    color: #1e293b;
    font-weight: 600;
    font-size: 1.05rem;
}

.security-info {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
}

.security-info i {
    font-size: 2.5rem;
    color: #667eea;
}

.security-info strong {
    color: #1e293b;
    display: block;
    margin-bottom: 5px;
}

.security-info p {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

@media (max-width: 1024px) {
    .profile-container {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function openEditModal() {
    document.getElementById('editModal').classList.add('active');
}

function openPasswordModal() {
    document.getElementById('passwordModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
</script>

<?php include 'includes/footer.php'; ?>