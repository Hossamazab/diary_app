<?php
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_GET['id'])) {
    redirect('user/dashboard.php', 'Invalid diary entry ID.');
}

$diaryId = $_GET['id'];
$userId = getUserId();

// First, fetch the diary to get the image path
try {
    $stmt = $pdo->prepare("SELECT image_path FROM diaries WHERE id = ? AND user_id = ?");
    $stmt->execute([$diaryId, $userId]);
    $diary = $stmt->fetch();
    
    if (!$diary) {
        redirect('user/dashboard.php', 'Diary entry not found or you do not have permission to delete it.');
    }
} catch (PDOException $e) {
    redirect('user/dashboard.php', 'Database error: ' . $e->getMessage());
}

// Delete the diary entry
try {
    $stmt = $pdo->prepare("DELETE FROM diaries WHERE id = ? AND user_id = ?");
    $stmt->execute([$diaryId, $userId]);
    
    // Delete the associated image if it exists
    if ($diary['image_path'] && file_exists(__DIR__ . '/../' . $diary['image_path'])) {
        unlink(__DIR__ . '/../' . $diary['image_path']);
    }
    
    redirect('user/dashboard.php', 'Diary entry deleted successfully.');
} catch (PDOException $e) {
    redirect('user/dashboard.php', 'Error deleting diary entry: ' . $e->getMessage());
}
?>