<?php
session_start();

include ('../includes/navbar.php'); 

// Pengecekan apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pengecekan apakah pengguna adalah user
if ($_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}



?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="manifest" href="../manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        /* Custom CSS untuk membuat layout lebih menarik */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 30px;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 30px;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-6 {
            margin-bottom: 20px;
        }

        /* Menambahkan space dan border radius untuk kesan lebih elegan */
        .card {
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Dashboard User</h1>
        </div>
    </header>

    <main class="container py-5">
        <h2>Selamat datang, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Ini adalah halaman user yang hanya bisa diakses oleh pengguna dengan hak akses user.</p>
        
        <!-- Kelola Santri -->
        <h3 class="mt-5 mb-3"><i class="fas fa-users-cog"></i> Kelola Santri</h3>
        <div class="row">
            <?php
            $santri_menus = [
                [
                    'title' => 'Tambah Santri',
                    'description' => 'Menambahkan santri baru ke dalam database.',
                    'icon' => 'fas fa-user-plus',
                    'color' => 'linear-gradient(135deg, #28a745, #218838)',
                    'link' => '../tingkat/tambah_santri.php'
                ],
                [
                    'title' => 'Daftar Santri',
                    'description' => 'Lihat daftar santri yang terdaftar di TPA.',
                    'icon' => 'fas fa-users',
                    'color' => 'linear-gradient(135deg, #17a2b8, #138496)',
                    'link' => '../tingkat/daftar_santri.php'
                ],
            ];

            foreach ($santri_menus as $menu): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-lg border-0 rounded-3">
                        <div class="card-body text-white rounded" style="background: <?= $menu['color']; ?>;">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-white text-dark me-3 shadow-sm">
                                    <i class="<?= $menu['icon']; ?> fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0"><?= $menu['title']; ?></h5>
                                    <p class="card-text"><?= $menu['description']; ?></p>
                                </div>
                            </div>
                            <a href="<?= $menu['link']; ?>" class="btn btn-outline-light mt-3 w-100 stretched-link">
                                Akses <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Kelola Presensi -->
        <h3 class="mt-5 mb-3"><i class="fas fa-clipboard-check"></i> Kelola Presensi</h3>
        <div class="row">
            <?php
            $presensi_menus = [
                [
                    'title' => 'Input Presensi',
                    'description' => 'Mencatat kehadiran santri per kelas.',
                    'icon' => 'fas fa-clipboard-list',
                    'color' => 'linear-gradient(135deg, #007bff, #0056b3)',
                    'link' => '../tingkat/presensi_santri.php'
                ],
                [
                    'title' => 'Data Presensi',
                    'description' => 'Lihat dan filter data kehadiran santri.',
                    'icon' => 'fas fa-table',
                    'color' => 'linear-gradient(135deg, #17a2b8, #138496)',
                    'link' => '../tingkat/tampilkan_presensi.php'
                ],
                [
                    'title' => 'Hapus Presensi',
                    'description' => 'Hapus data presensi per kelas atau per santri.',
                    'icon' => 'fas fa-trash-alt',
                    'color' => 'linear-gradient(135deg, #dc3545, #c82333)',
                    'link' => '../tingkat/hapus_presensi.php'
                ],
            ];

            foreach ($presensi_menus as $menu): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-lg border-0 rounded-3">
                        <div class="card-body text-white rounded" style="background: <?= $menu['color']; ?>;">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-white text-dark me-3 shadow-sm">
                                    <i class="<?= $menu['icon']; ?> fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0"><?= $menu['title']; ?></h5>
                                    <p class="card-text"><?= $menu['description']; ?></p>
                                </div>
                            </div>
                            <a href="<?= $menu['link']; ?>" class="btn btn-outline-light mt-3 w-100 stretched-link">
                                Akses <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
         
        
    </main>

    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('../sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration);
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        }
    </script>
</body>
</html>
