<?php
require_once '../config/config.php';

$page_title = 'All Children';

$db = new Database();
$conn = $db->getConnection();

// Get all children with parent details
$stmt = $conn->query("
    SELECT c.*, p.name as parent_name, p.email as parent_email, p.phone as parent_phone
    FROM child c
    JOIN parent p ON c.parent_id = p.parent_id
    ORDER BY c.child_id DESC
");
$children = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Registered Children</h3>
        <div>
            <span class="badge badge-info">Total: <?php echo count($children); ?></span>
        </div>
    </div>
    
    <?php if (count($children) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Child ID</th>
                        <th>Child Name</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                        <th>Parent Details</th>
                      
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($children as $child): ?>
                        <tr>
                            <td><strong>#<?php echo $child['child_id']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($child['child_name']); ?></strong></td>
                            <td><?php echo $child['gender']; ?></td>
                            <td><?php echo formatDate($child['date_of_birth']); ?></td>
                            <td><?php echo calculateAge($child['date_of_birth']); ?></td>
                            <td><span class="badge badge-info"><?php echo $child['blood_group']; ?></span></td>
                            <td>
                                <strong><?php echo htmlspecialchars($child['parent_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($child['parent_email']); ?></small><br>
                                <small><?php echo htmlspecialchars($child['parent_phone']); ?></small>
                            </td>
                          
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 40px;">No children registered yet</p>
    <?php endif; ?>
</div>


<?php include 'includes/footer.php'; ?>