<div class="sidebar">
    <div class="sidebar-header">
        <h2>⚙️ Admin Panel</h2>
        <p><?php echo SITE_NAME; ?></p>
    </div>
    
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="children.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'children.php' ? 'active' : ''; ?>">
            <i class="fas fa-baby"></i>
            <span>All Children</span>
        </a>
        
        <a href="vaccinations.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'vaccinations.php' ? 'active' : ''; ?>">
            <i class="fas fa-syringe"></i>
            <span>Vaccinations</span>
        </a>
        
        <a href="vaccines.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'vaccines.php' ? 'active' : ''; ?>">
            <i class="fas fa-vial"></i>
            <span>Vaccine Inventory</span>
        </a>
        
        <a href="requests.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Parent Requests</span>
        </a>
        
        <a href="bookings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        
        <a href="hospitals.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'hospitals.php' ? 'active' : ''; ?>">
            <i class="fas fa-hospital"></i>
            <span>Hospitals</span>
        </a>
        
        <a href="reports.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </div>
</div>