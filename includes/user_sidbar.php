<?php
// includes/sidebar.php
if (!isset($_SESSION['user_id'])) {
    return; // Don't show sidebar if not logged in
}
?>
س
<aside class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0 text-primary">
            <i class="fas fa-book-open me-2"></i> 
            <?php echo $_SESSION['user_type'] === 'admin' ? 'Admin Panel' : 'My Diary'; ?>
        </h4>
        <button class="sidebar-toggle d-lg-none">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-menu">
        <?php if ($_SESSION['user_type'] === 'admin'): ?>
            <!-- Admin Menu -->
            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" 
               class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="sidebar-icon fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/users.php" 
               class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                <i class="sidebar-icon fas fa-users"></i>
                <span>User Management</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/stats.php" 
               class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'stats.php' ? 'active' : ''; ?>">
                <i class="sidebar-icon fas fa-chart-bar"></i>
                <span>Statistics</span>
            </a>
        <?php else: ?>
            <!-- User Menu -->
            <a href="<?php echo BASE_URL; ?>/user/dashboard.php" 
               class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="sidebar-icon fas fa-home"></i>
                <span>My Diary</span>
            </a>
            <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#addDiaryModal">
                <i class="sidebar-icon fas fa-plus-circle"></i>
                <span>New Entry</span>
            </a>
        <?php endif; ?>
        
        <div class="px-3 mt-3 mb-1 text-muted small fw-bold">ACCOUNT</div>
        <a href="<?php echo BASE_URL; ?>/logout.php" class="sidebar-link">
            <i class="sidebar-icon fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Overlay for mobile -->
<div class="sidebar-overlay"></div>