<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default page title if not defined
if (!isset($pageTitle)) {
    $pageTitle = 'My Diary App';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">My Diary</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['user_type'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin Dashboard</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/user/dashboard.php">My Diary</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Sidebar - Only show when logged in -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <?php if($_SESSION['user_type'] === 'admin'): ?>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" 
                                   href="<?php echo BASE_URL; ?>/admin/dashboard.php">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" 
                                   href="<?php echo BASE_URL; ?>/admin/users.php">
                                    <i class="fas fa-users me-2"></i>User Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'stats.php' ? 'active' : ''; ?>" 
                                   href="<?php echo BASE_URL; ?>/admin/stats.php">
                                    <i class="fas fa-chart-bar me-2"></i>Statistics
                                </a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header bg-white">
                                <h5>My Diary</h5>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#addDiaryModal">
                                    <i class="fas fa-plus me-2"></i>Add New Entry
                                </button>
                                
                                <!-- Year/Month/Day Navigation would go here -->
                                <!-- You can include it separately or add the code here -->
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="<?php echo isset($_SESSION['user_id']) ? 'col-md-9 ms-sm-auto col-lg-10' : 'col-12'; ?> px-md-4">
                <div class="container my-4 flex-grow-1">
                    <?php 
                    // Display flash messages
                    if (isset($_SESSION['message'])) {
                        echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    ?>