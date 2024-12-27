<?php
// Mulai sesi hanya jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna sudah login
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? null; // Ambil role dari sesi
?>

<div class="sidebar bg-dark text-light">
    <h4 class="text-center py-3">
        <a href="home.php" class="text-decoration-none text-light">SISTEM ADMINISTRASI L/C</a>
    </h4>
    <hr class="bg-secondary">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="home" class="nav-link text-light">
                <i class="bi bi-house-door-fill"></i> Home
            </a>
        </li>
        <?php if ($is_logged_in && $user_role === 'admin'): ?>
            <li class="nav-item">
                <a href="user-management" class="nav-link text-light">
                    <i class="bi bi-people-fill"></i> User Management
                </a>
            </li>
        <?php endif; ?>
        <?php if ($is_logged_in): ?>
            <hr class="bg-secondary">
            <h5 class="px-3">Input Data</h5>
            <li class="nav-item">
                <a href="sertifikat-lc" class="nav-link text-light">
                    <i class="bi bi-file-earmark-text-fill"></i> Sertifikat L/C
                </a>
            </li>
            <li class="nav-item">
                <a href="term-of-payment" class="nav-link text-light">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Term Of Payment
                </a>
            </li>
            <hr class="bg-secondary">
            <h5 class="px-3">Monitoring</h5>
            <li class="nav-item">
                <a href="pagu-dipa" class="nav-link text-light">
                    <i class="bi bi-bar-chart-line-fill"></i> Pagu DIPA
                </a>
            </li>
            <li class="nav-item">
                <a href="realisasi" class="nav-link text-light">
                    <i class="bi bi-graph-up-arrow"></i> Realisasi
                </a>
            </li>
            <hr class="bg-secondary">
            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        <?php else: ?>
            <div class="alert alert-warning mx-3 mt-3">
                Silakan login terlebih dahulu untuk mengakses menu.
            </div>
            <li class="nav-item">
                <a href="login" class="nav-link text-light">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>