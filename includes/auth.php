<?php
// includes/auth.php
require_once __DIR__ . '/functions.php';

// Skip redirection for these pages
$allowed_pages = ['login.php', 'register.php', 'forgot-password.php'];

// Check if current page is allowed
$current_page = basename($_SERVER['PHP_SELF']);

if (!isLoggedIn() && !in_array($current_page, $allowed_pages)) {
    redirect('login.php', 'Please login to access this page.');
    exit; // Add exit to prevent further execution
}

// Admin pages check only if logged in
if (isLoggedIn()) {
    $admin_pages = ['dashboard.php', 'users.php', 'stats.php', 'edit_user.php'];
    // $current_page = basename($_SERVER['PHP_SELF']);
    if (in_array($current_page, $admin_pages) && !isAdmin()) {
        redirect('user/dashboard.php', 'You do not have permission to access that page.');
        exit; // Add exit to prevent further execution
    }
}
?>