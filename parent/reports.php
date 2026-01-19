<?php
require_once '../config/config.php';

if (!isLoggedIn('parent')) {
    redirect('login.php');
}

$page_title = 'Vaccination Reports';
$parent_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Get all completed vaccinations
$stmt = $conn->prepare("
    SELECT b.*, 
           c.child_name, c.gender, c.date_of_birth, c.blood_group,
           v.vaccine_name, v.description,
           h.hospital_name, h.location
    FROM booking b
    JOIN child c ON b.child_id = c.child_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    JOIN hospital h ON b.hospital_id = h.hospital_id
    WHERE b.parent_id = ? AND b.status = 'Completed'
    ORDER BY b.booking_date DESC
");
$stmt->execute([$parent_id]);
$completed_vaccinations = $stmt->fetchAll();

// Get children for filtering
$stmt = $conn->prepare("SELECT * FROM child WHERE parent_id = ?");
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-file-medical"></i> Vaccination Reports</h1>
    <button class="btn btn-primary" onclick="window.print()">
        <i class="fas fa-print"></i> Print Report
    </button>
</div>

<?php if (count($completed_vaccinations) > 0): ?>
    <!-- Filter by Child -->
    <div class="filter-section">
        <label><i class="fas fa-filter"></i> Filter by Child:</label>
        <select id="childFilter" class="form-control" onchange="filterByChild()" style="max-width: 300px;">
            <option value="all">All Children</option>
            <?php foreach ($children as $child): ?>
                <option value="<?php echo $child['child_id']; ?>">
                    <?php echo htmlspecialchars($child['child_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="reports-grid">
        <?php foreach ($completed_vaccinations as $vaccination): ?>
            <div class="report-card" data-child-id="<?php echo $vaccination['child_id']; ?>">
                <div class="report-header">
                    <div class="report-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>Completed</span>
                    </div>
                    <div class="report-date">
                        <?php echo formatDate($vaccination['booking_date']); ?>
                    </div>
                </div>
                
                <div class="report-body">
                    <div class="child-section">
                        <div class="child-avatar-small">
                            <i class="fas fa-child"></i>
                        </div>
                        <div>
                            <h3><?php echo htmlspecialchars($vaccination['child_name']); ?></h3>
                            <div class="child-meta">
                                <span><i class="fas fa-venus-mars"></i> <?php echo $vaccination['gender']; ?></span>
                                <span><i class="fas fa-birthday-cake"></i> <?php echo calculateAge($vaccination['date_of_birth']); ?></span>
                                <span><i class="fas fa-tint"></i> <?php echo $vaccination['blood_group']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vaccine-section">
                        <div class="vaccine-icon">
                            <i class="fas fa-syringe"></i>
                        </div>
                        <div>
                            <h4><?php echo htmlspecialchars($vaccination['vaccine_name']); ?></h4>
                            <p><?php echo htmlspecialchars($vaccination['description']); ?></p>
                        </div>
                    </div>
                    
                    <div class="hospital-section">
                        <div class="info-row">
                            <i class="fas fa-hospital"></i>
                            <span><?php echo htmlspecialchars($vaccination['hospital_name']); ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($vaccination['location']); ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-calendar-check"></i>
                            <span>Administered on <?php echo formatDate($vaccination['booking_date']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="report-footer">
                    <div class="certificate-badge">
                        <i class="fas fa-certificate"></i>
                        <span>Vaccination Certificate</span>
                    </div>
                  
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Summary Statistics -->
    <div class="summary-section">
        <h2><i class="fas fa-chart-pie"></i> Vaccination Summary</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value"><?php echo count($completed_vaccinations); ?></div>
                    <div class="summary-label">Total Vaccinations</div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-baby"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value"><?php echo count($children); ?></div>
                    <div class="summary-label">Children Vaccinated</div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-syringe"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">
                        <?php 
                        $unique_vaccines = array_unique(array_column($completed_vaccinations, 'vaccine_name'));
                        echo count($unique_vaccines);
                        ?>
                    </div>
                    <div class="summary-label">Different Vaccines</div>
                </div>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-file-medical-alt"></i>
        <h3>No Vaccination Records Yet</h3>
        <p>Completed vaccinations will appear here</p>
        <a href="appointments.php" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Book Appointment
        </a>
    </div>
<?php endif; ?>

<style>
.filter-section {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.filter-section label {
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 8px;
}

.reports-grid {
    display: grid;
    gap: 25px;
    margin-bottom: 40px;
}

.report-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border-left: 5px solid #10b981;
    transition: all 0.3s ease;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.report-header {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.report-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #065f46;
    font-weight: 600;
}

.report-badge i {
    font-size: 1.2rem;
}

.report-date {
    color: #059669;
    font-weight: 500;
}

.report-body {
    padding: 25px;
}

.child-section {
    display: flex;
    align-items: center;
    gap: 15px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f1f5f9;
    margin-bottom: 20px;
}

.child-avatar-small {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
}

.child-section h3 {
    color: #1e293b;
    margin-bottom: 8px;
}

.child-meta {
    display: flex;
    gap: 15px;
    font-size: 0.85rem;
    color: #64748b;
}

.child-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.vaccine-section {
    display: flex;
    gap: 15px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 20px;
}

.vaccine-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.vaccine-section h4 {
    color: #1e293b;
    margin-bottom: 5px;
}

.vaccine-section p {
    color: #64748b;
    font-size: 0.9rem;
}

.hospital-section {
    display: grid;
    gap: 12px;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748b;
    font-size: 0.95rem;
}

.info-row i {
    color: #667eea;
    width: 20px;
}

.report-footer {
    padding: 20px;
    background: #f8fafc;
    border-top: 2px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.certificate-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #f59e0b;
    font-weight: 600;
}

.certificate-badge i {
    font-size: 1.2rem;
}

.summary-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 30px;
    border-radius: 15px;
    margin-top: 40px;
}

.summary-section h2 {
    color: #1e293b;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.summary-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
}

.summary-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
}

.summary-label {
    color: #64748b;
    font-size: 0.9rem;
}

@media print {
    .page-header button,
    .filter-section,
    .report-footer button,
    .top-nav,
    .btn {
        display: none !important;
    }
    
    .report-card {
        page-break-inside: avoid;
        margin-bottom: 20px;
    }
}

@media (max-width: 768px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .child-section {
        flex-direction: column;
        text-align: center;
    }
    
    .child-meta {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<script>
function filterByChild() {
    const selectedChild = document.getElementById('childFilter').value;
    const cards = document.querySelectorAll('.report-card');
    
    cards.forEach(card => {
        if (selectedChild === 'all' || card.dataset.childId === selectedChild) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}


</script>

<?php include 'includes/footer.php'; ?>