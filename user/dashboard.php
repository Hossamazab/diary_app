<?php


// require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../includes/functions.php';


$pageTitle = 'My Diary';
$userId = getUserId();

// Get filter parameters
$year = $_GET['year'] ?? null;
$month = $_GET['month'] ?? null;
$day = $_GET['day'] ?? null;

// Get diaries for the user
$diaries = getDiariesByUser($userId, $year, $month, $day);

// Get available years for sidebar
$diaryYears = getDiaryYears($userId);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-white">
                <h5>My Diary</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#addDiaryModal">
                    <i class="fas fa-plus me-2"></i>Add New Entry
                </button>
                
                <!-- Year/Month/Day Navigation -->
                <div class="accordion" id="diaryAccordion">
                    <?php foreach ($diaryYears as $diaryYear): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#year<?php echo $diaryYear; ?>" aria-expanded="false">
                                <?php echo $diaryYear; ?>
                            </button>
                        </h2>
                        <div id="year<?php echo $diaryYear; ?>" class="accordion-collapse collapse" 
                            data-bs-parent="#diaryAccordion">
                            <div class="accordion-body p-0">
                                <div class="accordion" id="monthAccordion<?php echo $diaryYear; ?>">
                                    <?php
                                    // Get months with diaries for this year
                                    $stmt = $pdo->prepare("SELECT DISTINCT MONTH(date) as month FROM diaries 
                                                          WHERE user_id = ? AND YEAR(date) = ? ORDER BY month DESC");
                                    $stmt->execute([$userId, $diaryYear]);
                                    $months = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                    
                                    foreach ($months as $monthNum):
                                        $monthName = date('F', mktime(0, 0, 0, $monthNum, 1));
                                    ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#month<?php echo $diaryYear . $monthNum; ?>">
                                                <?php echo $monthName; ?>
                                            </button>
                                        </h2>
                                        <div id="month<?php echo $diaryYear . $monthNum; ?>" 
                                            class="accordion-collapse collapse" 
                                            data-bs-parent="#monthAccordion<?php echo $diaryYear; ?>">
                                            <div class="accordion-body p-0">
                                                <ul class="list-group list-group-flush">
                                                    <?php
                                                    // Get days with diaries for this month/year
                                                    $stmt = $pdo->prepare("SELECT DISTINCT DAY(date) as day FROM diaries 
                                                                          WHERE user_id = ? AND YEAR(date) = ? 
                                                                          AND MONTH(date) = ? ORDER BY day DESC");
                                                    $stmt->execute([$userId, $diaryYear, $monthNum]);
                                                    $days = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                                    
                                                    foreach ($days as $dayNum):
                                                    ?>
                                                    <li class="list-group-item">
                                                        <a href="dashboard.php?year=<?php echo $diaryYear; ?>&month=<?php echo $monthNum; ?>&day=<?php echo $dayNum; ?>" 
                                                           class="text-decoration-none">
                                                            <?php echo $dayNum . date('S', mktime(0, 0, 0, 0, $dayNum)); ?>
                                                        </a>
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <?php if ($year && $month && $day): ?>
                    Entries for <?php echo date('F j, Y', mktime(0, 0, 0, $month, $day, $year)); ?>
                <?php elseif ($year && $month): ?>
                    Entries for <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
                <?php elseif ($year): ?>
                    Entries for <?php echo $year; ?>
                <?php else: ?>
                    Recent Entries
                <?php endif; ?>
            </h2>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Sort by: Newest First
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" href="#">Newest First</a></li>
                    <li><a class="dropdown-item" href="#">Oldest First</a></li>
                    <li><a class="dropdown-item" href="#">By Title</a></li>
                </ul>
            </div>
        </div>
        

        <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <?php if ($year && $month && $day): ?>
            Entries for <?php echo date('F j, Y', mktime(0, 0, 0, $month, $day, $year)); ?>
        <?php elseif ($year && $month): ?>
            Entries for <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
        <?php elseif ($year): ?>
            Entries for <?php echo $year; ?>
        <?php else: ?>
            My Diary Entries
        <?php endif; ?>
    </h2>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="all">
            <i class="fas fa-list"></i> All
        </button>
        <button type="button" class="btn btn-outline-warning filter-btn" data-filter="starred">
            <i class="fas fa-star"></i> Starred Only
        </button>
    </div>
</div>

        <!-- Diary Cards -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
    <?php foreach ($diaries as $diary): ?>
    <div class="col">
        <div class="card h-100 shadow-sm diary-card">
            <?php if (!empty($diary['image_path'])): ?>
            <img src="<?php echo BASE_URL . '/' . $diary['image_path']; ?>" class="card-img-top" alt="Diary image">
            <?php endif; ?>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title"><?php echo htmlspecialchars($diary['title']); ?></h5>
                    <button class="btn btn-sm btn-star" data-diary-id="<?php echo $diary['id']; ?>">
                        <i class="fas fa-star<?php echo $diary['is_starred'] ? ' text-warning' : ''; ?>"></i>
                    </button>
                </div>
                <h6 class="card-subtitle mb-2 text-muted">
                    <?php echo date('F j, Y', strtotime($diary['date'])); ?>
                </h6>
                <p class="card-text"><?php echo substr(htmlspecialchars($diary['content']), 0, 150); ?>...</p>
                <div class="d-flex justify-content-between">
                    <a href="view_diary.php?id=<?php echo $diary['id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                    <div>
                        <a href="edit_diary.php?id=<?php echo $diary['id']; ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_diary.php?id=<?php echo $diary['id']; ?>" class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('Are you sure you want to delete this entry?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>    </div>
</div>

<!-- Add Diary Modal -->
<div class="modal fade" id="addDiaryModal" tabindex="-1" aria-labelledby="addDiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDiaryModalLabel">New Diary Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_diary.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="diaryTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="diaryTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="diaryDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="diaryDate" name="date" required 
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="diaryContent" class="form-label">Content</label>
                        <textarea class="form-control" id="diaryContent" name="content" rows="8" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="diaryImage" class="form-label">Upload Image (optional)</label>
                        <input class="form-control" type="file" id="diaryImage" name="image" accept="image/*">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="isPublic" name="is_public">
                        <label class="form-check-label" for="isPublic">Make this entry public</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>