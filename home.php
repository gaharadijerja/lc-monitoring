<?php
session_start();

// Check login status
$is_logged_in = isset($_SESSION['user_id']);

// Redirect to login page if not logged in
if (!$is_logged_in) {
    $message = "Silakan login terlebih dahulu untuk mengakses menu.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
<link rel="icon" type="image/png" href="/images/myicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Administrasi L/C</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .logout-btn {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            background-color: #dc3545;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #b02a37;
        }
        .alert {
            margin-top: 20px;
        }        
        .app-image {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1 class="text-center mb-4">
                SISTEM ADMINISTRASI PROSES PENGESAHAN BELANJA <br> 
                PINJAMAN LUAR NEGERI via LETTER of CREDIT (L/C)
            </h1>
            <p class="text-center">
                <?php if (!$is_logged_in): ?>
                    Anda belum login. Silakan login untuk menggunakan sistem.
                <?php else: ?>
                    Selamat datang di sistem administrasi untuk proses pengesahan belanja pinjaman luar negeri. Pilih menu navigasi di samping untuk melanjutkan.
                <?php endif; ?>
    <!-- Display Image -->
                <img src="images/home2.jpg" alt="Home Image" class="app-image">
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
