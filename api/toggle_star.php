<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = getUserId();
$diaryId = $_POST['diary_id'] ?? null;

if (!$userId || !$diaryId) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Check if diary belongs to user
    $stmt = $pdo->prepare("SELECT is_starred FROM diaries WHERE id = ? AND user_id = ?");
    $stmt->execute([$diaryId, $userId]);
    $diary = $stmt->fetch();
    
    if (!$diary) {
        echo json_encode(['success' => false, 'message' => 'Diary not found']);
        exit;
    }
    
    // Toggle star status
    $newStatus = !$diary['is_starred'];
    $stmt = $pdo->prepare("UPDATE diaries SET is_starred = ? WHERE id = ?");
    $stmt->execute([$newStatus, $diaryId]);
    
    echo json_encode(['success' => true, 'is_starred' => $newStatus]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}