<?php
require_once '../config/config.php';

if (!isLoggedIn('parent')) {
    redirect('login.php');
}

$page_title = 'My Children';
$parent_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Handle add/edit child
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $child_name = sanitize($_POST['child_name']);
            $gender = sanitize($_POST['gender']);
            $dob = sanitize($_POST['date_of_birth']);
            $blood_group = sanitize($_POST['blood_group']);
            
            $stmt = $conn->prepare("INSERT INTO child (parent_id, child_name, gender, date_of_birth, blood_group) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$parent_id, $child_name, $gender, $dob, $blood_group])) {
                setFlashMessage('success', 'Child added successfully!');
            } else {
                setFlashMessage('danger', 'Failed to add child.');
            }
            redirect('children.php');
        } elseif ($_POST['action'] === 'edit') {
            $child_id = intval($_POST['child_id']);
            $child_name = sanitize($_POST['child_name']);
            $gender = sanitize($_POST['gender']);
            $dob = sanitize($_POST['date_of_birth']);
            $blood_group = sanitize($_POST['blood_group']);
            
            $stmt = $conn->prepare("UPDATE child SET child_name = ?, gender = ?, date_of_birth = ?, blood_group = ? WHERE child_id = ? AND parent_id = ?");
            if ($stmt->execute([$child_name, $gender, $dob, $blood_group, $child_id, $parent_id])) {
                setFlashMessage('success', 'Child updated successfully!');
            } else {
                setFlashMessage('danger', 'Failed to update child.');
            }
            redirect('children.php');
        }
    }
}

// Get all children
$stmt = $conn->prepare("SELECT * FROM child WHERE parent_id = ? ORDER BY child_id DESC");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-baby"></i> My Children</h1>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="fas fa-plus"></i> Add Child
    </button>
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

<?php if (count($children) > 0): ?>
    <div class="children-grid">
        <?php foreach ($children as $child): ?>
            <div class="child-card">
                <div class="child-avatar">
                    <i class="fas fa-child"></i>
                </div>
                <div class="child-info">
                    <h3><?php echo htmlspecialchars($child['child_name']); ?></h3>
                    <div class="child-details">
                        <div class="detail-item">
                            <i class="fas fa-venus-mars"></i>
                            <span><?php echo $child['gender']; ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-birthday-cake"></i>
                            <span><?php echo formatDate($child['date_of_birth']); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo calculateAge($child['date_of_birth']); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <span><?php echo $child['blood_group']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="child-actions">
                    <button class="btn btn-sm btn-primary" onclick='openEditModal(<?php echo json_encode($child); ?>)'>
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-baby"></i>
        <h3>No Children Added Yet</h3>
        <p>Add your first child to start managing vaccinations</p>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add Your First Child
        </button>
    </div>
<?php endif; ?>

<!-- Add Child Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Add New Child</h3>
            <button class="close-modal" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label>Child Name *</label>
                <input type="text" name="child_name" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Gender *</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Blood Group *</label>
                    <select name="blood_group" class="form-control" required>
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="date_of_birth" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Child
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Child Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Child Details</h3>
            <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="child_id" id="edit_child_id">
            
            <div class="form-group">
                <label>Child Name *</label>
                <input type="text" name="child_name" id="edit_child_name" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Gender *</label>
                    <select name="gender" id="edit_gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Blood Group *</label>
                    <select name="blood_group" id="edit_blood_group" class="form-control" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="date_of_birth" id="edit_dob" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Child
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.add('active');
}

function openEditModal(child) {
    document.getElementById('edit_child_id').value = child.child_id;
    document.getElementById('edit_child_name').value = child.child_name;
    document.getElementById('edit_gender').value = child.gender;
    document.getElementById('edit_blood_group').value = child.blood_group;
    document.getElementById('edit_dob').value = child.date_of_birth;
    document.getElementById('editModal').classList.add('active');
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