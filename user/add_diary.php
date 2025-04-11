<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = getUserId();
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $date = $_POST['date'];
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    
    // Handle file upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = 'assets/uploads/' . $filename;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO diaries (user_id, title, content, date, image_path, is_public) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $title, $content, $date, $imagePath, $isPublic]);
        
        redirect('user/dashboard.php', 'Diary entry added successfully!');
    } catch (PDOException $e) {
        // Delete the uploaded file if database insert failed
        if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
            unlink(__DIR__ . '/../' . $imagePath);
        }
        redirect('user/dashboard.php', 'Error adding diary entry: ' . $e->getMessage());
    }
} else {
    redirect('user/dashboard.php');
}
?>