<?php
require_once __DIR__ . '/../includes/auth.php';

// Get statistics
try {
    // Users per month
    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                         FROM users GROUP BY month ORDER BY month");
    $usersPerMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Diaries per month
    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                         FROM diaries GROUP BY month ORDER BY month");
    $diariesPerMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top users with most diaries
    $stmt = $pdo->query("SELECT u.username, COUNT(d.id) as diary_count 
                         FROM users u LEFT JOIN diaries d ON u.id = d.user_id 
                         GROUP BY u.id ORDER BY diary_count DESC LIMIT 5");
    $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$pageTitle = 'Statistics';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Statistics</h1>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Registrations Per Month</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="usersChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Diary Entries Per Month</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="diariesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Users by Diary Entries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Diary Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo $user['diary_count']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Users per month chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    const usersChart = new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($usersPerMonth, 'month')); ?>,
            datasets: [{
                label: 'User Registrations',
                data: <?php echo json_encode(array_column($usersPerMonth, 'count')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Diaries per month chart
    const diariesCtx = document.getElementById('diariesChart').getContext('2d');
    const diariesChart = new Chart(diariesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($diariesPerMonth, 'month')); ?>,
            datasets: [{
                label: 'Diary Entries',
                data: <?php echo json_encode(array_column($diariesPerMonth, 'count')); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>