</div> <!-- Close container div -->
        
        <footer class="bg-dark text-white py-4 mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>My Diary App</h5>
                        <p>Capture your thoughts and memories in a secure, private space.</p>
                    </div>
                    <div class="col-md-3">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo BASE_URL; ?>/index.php" class="text-white text-decoration-none">Home</a></li>
                            <li><a href="#" class="text-white text-decoration-none">About</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h5>Connect</h5>
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> My Diary App. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Bootstrap 5 JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Custom JS -->
        <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
        <script>
    // User sidebar toggle
    document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
        document.querySelector('.user-sidebar').classList.toggle('active');
        document.querySelector('.sidebar-overlay').classList.toggle('active');
    });

    // Close sidebar when clicking overlay
    document.querySelector('.sidebar-overlay')?.addEventListener('click', function() {
        document.querySelector('.user-sidebar').classList.remove('active');
        this.classList.remove('active');
    });
</script>
<script>
// Toggle star status
document.querySelectorAll('.btn-star').forEach(btn => {
    btn.addEventListener('click', async function() {
        const diaryId = this.dataset.diaryId;
        const icon = this.querySelector('i');
        
        try {
            const response = await fetch('<?php echo BASE_URL; ?>/api/toggle_star.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `diary_id=${diaryId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                icon.classList.toggle('text-warning');
            } else {
                alert(data.message || 'Error updating star status');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred');
        }
    });
});

// Filter diaries
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        
        // Update active button state
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active');
        });
        this.classList.add('active');
        
        // Filter diaries
        document.querySelectorAll('.diary-card').forEach(card => {
            const isStarred = card.querySelector('.fa-star').classList.contains('text-warning');
            
            if (filter === 'all' || (filter === 'starred' && isStarred)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

    </body>
</html>