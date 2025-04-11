<?php
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_GET['id'])) {
    redirect('user/dashboard.php', 'Invalid diary entry ID.');
}

$diaryId = $_GET['id'];
$userId = getUserId();

try {
    $stmt = $pdo->prepare("SELECT * FROM diaries WHERE id = ? AND user_id = ?");
    $stmt->execute([$diaryId, $userId]);
    $diary = $stmt->fetch();
    
    if (!$diary) {
        redirect('user/dashboard.php', 'Diary entry not found or you do not have permission to view it.');
    }
} catch (PDOException $e) {
    redirect('user/dashboard.php', 'Database error: ' . $e->getMessage());
}

$pageTitle = $diary['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <?php if (!empty($diary['image_path'])): ?>
            <img src="<?php echo BASE_URL . '/' . $diary['image_path']; ?>" class="card-img-top" 
                 alt="Diary image" style="max-height: 400px; object-fit: cover;">
            <?php endif; ?>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="card-title"><?php echo htmlspecialchars($diary['title']); ?></h1>
                    <div>
                        <a href="edit_diary.php?id=<?php echo $diary['id']; ?>" 
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete_diary.php?id=<?php echo $diary['id']; ?>" 
                           class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('Are you sure you want to delete this entry?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
                <h6 class="card-subtitle mb-4 text-muted">
                    <?php echo date('F j, Y', strtotime($diary['date'])); ?>
                    <?php if ($diary['is_public']): ?>
                        <span class="badge bg-success ms-2">Public</span>
                    <?php else: ?>
                        <span class="badge bg-secondary ms-2">Private</span>
                    <?php endif; ?>
                </h6>
                <div class="card-text">
                    <?php echo nl2br(htmlspecialchars($diary['content'])); ?>
                </div>
            </div>
            <div class="card-footer text-muted">
                Last updated: <?php echo date('F j, Y \a\t g:i a', strtotime($diary['updated_at'])); ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Diary
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>