<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../config.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $base_url; ?>/index.php">TPA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/index.php">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'activities.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/page/activities.php">
                        <i class="fas fa-calendar-alt"></i> Kegiatan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'schedule.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/page/schedule.php">
                        <i class="fas fa-clock"></i> Jadwal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'announcements.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/page/announcements.php">
                        <i class="fas fa-bullhorn"></i> Pengumuman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/page/contact.php">
                        <i class="fas fa-envelope"></i> Kontak
                    </a>
                </li>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard_user.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/page/dashboard_user.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>" href="<?php echo $base_url; ?>/login.php">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Tambahkan pustaka Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
