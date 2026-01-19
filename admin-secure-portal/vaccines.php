<?php
require_once '../config/config.php';

$page_title = 'Vaccine Inventory';

$db = new Database();
$conn = $db->getConnection();

// Handle add vaccine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $vaccine_name = sanitize($_POST['vaccine_name']);
    $description = sanitize($_POST['description']);
    
    $stmt = $conn->prepare("INSERT INTO vaccine (vaccine_name, description, availability) VALUES (?, ?, 'Available')");
    if ($stmt->execute([$vaccine_name, $description])) {
        setFlashMessage('success', 'Vaccine added successfully!');
    } else {
        setFlashMessage('danger', 'Failed to add vaccine.');
    }
    redirect('vaccines.php');
}

// Get all vaccines
$stmt = $conn->query("SELECT * FROM vaccine ORDER BY vaccine_name");
$vaccines = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Vaccine Inventory Management</h3>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">
            <i class="fas fa-plus"></i> Add Vaccine
        </button>
    </div>
    
    <?php if (count($vaccines) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Vaccine ID</th>
                        <th>Vaccine Name</th>
                        <th>Description</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vaccines as $vaccine): ?>
                        <tr>
                            <td><strong>#<?php echo $vaccine['vaccine_id']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($vaccine['vaccine_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($vaccine['description']); ?></td>
                            <td><?php echo getStatusBadge($vaccine['availability']); ?></td>
                            <td>
                                <button class="btn btn-sm <?php echo $vaccine['availability'] === 'Available' ? 'btn-warning' : 'btn-success'; ?>" 
                                        onclick="toggleAvailability(<?php echo $vaccine['vaccine_id']; ?>, '<?php echo $vaccine['availability']; ?>')">
                                    <?php echo $vaccine['availability'] === 'Available' ? 'Mark Unavailable' : 'Mark Available'; ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 40px;">No vaccines in inventory</p>
    <?php endif; ?>
</div>

<!-- Add Vaccine Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Vaccine</h3>
            <button class="close-modal" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label>Vaccine Name *</label>
                <input type="text" name="vaccine_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Vaccine</button>
        </form>
    </div>
</div>

<script>
function toggleAvailability(vaccineId, currentStatus) {
    const newStatus = currentStatus === 'Available' ? 'Unavailable' : 'Available';
    
    if (confirm(`Are you sure you want to mark this vaccine as ${newStatus}?`)) {
        fetch('ajax/update_vaccine.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `vaccine_id=${vaccineId}&availability=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>