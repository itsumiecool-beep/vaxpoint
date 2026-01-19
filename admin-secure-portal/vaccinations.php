<?php
require_once '../config/config.php';

$page_title = 'Vaccination Scheduling';

$db = new Database();
$conn = $db->getConnection();

// Get all vaccination schedules
$stmt = $conn->query("
    SELECT vs.*, 
           c.child_name, c.gender, c.date_of_birth,
           p.name as parent_name, p.email as parent_email,
           v.vaccine_name
    FROM vaccination_schedule vs
    JOIN child c ON vs.child_id = c.child_id
    JOIN parent p ON c.parent_id = p.parent_id
    JOIN vaccine v ON vs.vaccine_id = v.vaccine_id
    ORDER BY vs.scheduled_date ASC
");
$schedules = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Vaccination Schedule</h3>
        <div>
            <span class="badge badge-warning">Upcoming: <?php echo count(array_filter($schedules, fn($s) => strtotime($s['scheduled_date']) >= time())); ?></span>
            <span class="badge badge-info">Past: <?php echo count(array_filter($schedules, fn($s) => strtotime($s['scheduled_date']) < time())); ?></span>
        </div>
    </div>
    
    <?php if (count($schedules) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Schedule ID</th>
                        <th>Child Details</th>
                        <th>Parent</th>
                        <th>Vaccine</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <?php 
                        $is_upcoming = strtotime($schedule['scheduled_date']) >= time();
                        $row_class = $is_upcoming ? 'style="background: #fef3c7;"' : '';
                        ?>
                        <tr <?php echo $row_class; ?>>
                            <td><strong>#<?php echo $schedule['schedule_id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($schedule['child_name']); ?></strong><br>
                                <small><?php echo $schedule['gender']; ?> | <?php echo calculateAge($schedule['date_of_birth']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($schedule['parent_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($schedule['parent_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($schedule['vaccine_name']); ?></td>
                            <td><?php echo formatDate($schedule['scheduled_date']); ?></td>
                            <td>
                                <?php if ($is_upcoming): ?>
                                    <span class="badge badge-warning">Upcoming</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Past</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 40px;">No vaccination schedules found</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>