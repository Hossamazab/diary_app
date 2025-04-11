<?php
require_once __DIR__ . '/../config/database.php';

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Redirect with message
function redirect($location, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: " . BASE_URL . "/$location");
    exit();
}

// Display session message
function displayMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Get current user ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get diaries by user with filters
function getDiariesByUser($userId, $year = null, $month = null, $day = null) {
    global $pdo;
    
    $sql = "SELECT * FROM diaries WHERE user_id = :user_id";
    $params = [':user_id' => $userId];
    
    if ($year) {
        $sql .= " AND YEAR(date) = :year";
        $params[':year'] = $year;
    }
    
    if ($month) {
        $sql .= " AND MONTH(date) = :month";
        $params[':month'] = $month;
    }
    
    if ($day) {
        $sql .= " AND DAY(date) = :day";
        $params[':day'] = $day;
    }
    
    $sql .= " ORDER BY date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get available years with diaries for a user
function getDiaryYears($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT DISTINCT YEAR(date) as year FROM diaries WHERE user_id = ? ORDER BY year DESC");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>