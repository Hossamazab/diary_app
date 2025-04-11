<?php
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_GET['id'])) {
    redirect('user/dashboard.php', 'Invalid diary entry ID.');
}

$diaryId = $_GET['id'];
$userId = getUserId();

// Fetch the diary entry
try {
    $stmt = $pdo->prepare("SELECT * FROM diaries WHERE id = ? AND user_id = ?");
    $stmt->execute([$diaryId, $userId]);
    $diary = $stmt->fetch();
    
    if (!$diary) {
        redirect('user/dashboard.php', 'Diary entry not found or you do not have permission to edit it.');
    }
} catch (PDOException $e) {
    redirect('user/dashboard.php', 'Database error: ' . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $date = $_POST['date'];
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    $deleteImage = isset($_POST['delete_image']) ? 1 : 0;
    
    $imagePath = $diary['image_path'];
    
    // Handle file upload if new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if it exists
        if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
            unlink(__DIR__ . '/../' . $imagePath);
        }
        
        $uploadDir = __DIR__ . '/../assets/uploads/';
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false && move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'assets/uploads/' . $filename;
        }
    } elseif ($deleteImage && $imagePath) {
        // Delete the image if requested
        if (file_exists(__DIR__ . '/../' . $imagePath)) {
            unlink(__DIR__ . '/../' . $imagePath);
        }
        $imagePath = null;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE diaries SET title = ?, content = ?, date = ?, 
                              image_path = ?, is_public = ?, updated_at = CURRENT_TIMESTAMP 
                              WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $date, $imagePath, $isPublic, $diaryId, $userId]);
        
        redirect('view_diary.php?id=' . $diaryId, 'Diary entry updated successfully!');
    } catch (PDOException $e) {
        redirect('edit_diary.php?id=' . $diaryId, 'Error updating diary entry: ' . $e->getMessage());
    }
}

$pageTitle = 'Edit ' . $diary['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">Edit Diary Entry</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="diaryTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="diaryTitle" name="title" 
                               value="<?php echo htmlspecialchars($diary['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="diaryDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="diaryDate" name="date" 
                               value="<?php echo $diary['date']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="diaryContent" class="form-label">Content</label>
                        <textarea class="form-control" id="diaryContent" name="content" rows="8" 
                                  required><?php echo htmlspecialchars($diary['content']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <?php if (!empty($diary['image_path'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo BASE_URL . '/' . $diary['image_path']; ?>" 
                                     class="img-thumbnail" style="max-height: 200px;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="deleteImage" name="delete_image">
                                    <label class="form-check-label" for="deleteImage">Delete current image</label>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No image attached</p>
                        <?php endif; ?>
                        
                        <label for="diaryImage" class="form-label">Upload New Image (optional)</label>
                        <input class="form-control" type="file" id="diaryImage" name="image" accept="image/*">
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="isPublic" name="is_public" 
                               <?php echo $diary['is_public'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="isPublic">Make this entry public</label>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="view_diary.php?id=<?php echo $diaryId; ?>" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>