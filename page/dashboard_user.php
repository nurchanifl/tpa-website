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
        
        <div class="row mt-4">
            <!-- Tambah Santri -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-lg border-0 rounded-3 hover-zoom">
                    <div class="card-body text-white rounded" style="background: linear-gradient(135deg, #28a745, #218838);">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-white text-success me-3 shadow-sm">
                                <i class="fas fa-user-plus fs-3"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tambah Santri</h5>
                                <p class="card-text">Menambahkan santri baru ke dalam database.</p>
                            </div>
                        </div>
                        <a href="../tingkat/tambah_santri.php" class="btn btn-outline-light mt-3 w-100 stretched-link">
                            Tambahkan <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Daftar Santri -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-lg border-0 rounded-3 hover-zoom">
                    <div class="card-body text-white rounded" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-white text-info me-3 shadow-sm">
                                <i class="fas fa-users fs-3"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Daftar Santri</h5>
                                <p class="card-text">Lihat daftar santri yang terdaftar di TPA.</p>
                            </div>
                        </div>
                        <a href="../tingkat/daftar_santri.php" class="btn btn-outline-light mt-3 w-100 stretched-link">
                            Lihat Daftar <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Presensi Santri -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-lg border-0 rounded-3 hover-zoom">
                    <div class="card-body text-white rounded" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-white text-primary me-3 shadow-sm">
                                <i class="fas fa-clipboard-list fs-3"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Presensi Santri</h5>
                                <p class="card-text">Mencatat kehadiran santri per kelas.</p>
                            </div>
                        </div>
                        <a href="../tingkat/presensi_santri.php" class="btn btn-outline-light mt-3 w-100 stretched-link">
                            Presensi <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tampilkan Data Presensi -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-lg border-0 rounded-3 hover-zoom">
                    <div class="card-body text-white rounded" style="background: linear-gradient(135deg, #6c757d, #495057);">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-white text-secondary me-3 shadow-sm">
                                <i class="fas fa-table fs-3"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Tampilkan Data Presensi</h5>
                                <p class="card-text">Lihat dan filter data kehadiran santri.</p>
                            </div>
                        </div>
                        <a href="../tingkat/tampilkan_presensi.php" class="btn btn-outline-light mt-3 w-100 stretched-link">
                            Lihat Presensi <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

         <!-- Tampilkan Data Presensi -->
         
        
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
