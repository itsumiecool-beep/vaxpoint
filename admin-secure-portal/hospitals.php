<?php
require_once '../config/config.php';

$page_title = 'Hospital Management';

$db = new Database();
$conn = $db->getConnection();

// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $hospital_name = sanitize($_POST['hospital_name']);
            $email = sanitize($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $address = sanitize($_POST['address']);
            $location = sanitize($_POST['location']);
            
            $stmt = $conn->prepare("INSERT INTO hospital (hospital_name, email, password, address, location, status) VALUES (?, ?, ?, ?, ?, 'Active')");
            if ($stmt->execute([$hospital_name, $email, $password, $address, $location])) {
                setFlashMessage('success', 'Hospital added successfully!');
            } else {
                setFlashMessage('danger', 'Failed to add hospital.');
            }
            redirect('hospitals.php');
        } elseif ($_POST['action'] === 'update_status') {
            $hospital_id = intval($_POST['hospital_id']);
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE hospital SET status = ? WHERE hospital_id = ?");
            if ($stmt->execute([$status, $hospital_id])) {
                setFlashMessage('success', 'Hospital status updated!');
            }
            redirect('hospitals.php');
        }
    }
}

// Get all hospitals
$stmt = $conn->query("SELECT * FROM hospital ORDER BY hospital_name");
$hospitals = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hospital Management</h3>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">
            <i class="fas fa-plus"></i> Add Hospital
        </button>
    </div>
    
    <?php if (count($hospitals) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hospital Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hospitals as $hospital): ?>
                        <tr>
                            <td><?php echo $hospital['hospital_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($hospital['hospital_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($hospital['email']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['address']); ?></td>
                            <td><?php echo htmlspecialchars($hospital['location']); ?></td>
                            <td><?php echo getStatusBadge($hospital['status']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="hospital_id" value="<?php echo $hospital['hospital_id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $hospital['status'] === 'Active' ? 'Inactive' : 'Active'; ?>">
                                    <button type="submit" class="btn btn-sm <?php echo $hospital['status'] === 'Active' ? 'btn-warning' : 'btn-success'; ?>">
                                        <?php echo $hospital['status'] === 'Active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 40px;">No hospitals found</p>
    <?php endif; ?>
</div>

<!-- Add Hospital Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Hospital</h3>
            <button class="close-modal" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label>Hospital Name *</label>
                <input type="text" name="hospital_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Address *</label>
                <textarea name="address" class="form-control" rows="2" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Location/City *</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Hospital</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>