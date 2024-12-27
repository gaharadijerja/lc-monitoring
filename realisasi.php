<?php
session_start(); // Memulai sesi

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php?error=not_logged_in");
    exit();
}

// Database connection
include 'koneksi.php';

// Example values, replace with actual dynamic data
$totalPagu = 17005027704000;
$jumlahRealisasi = 1986495623150;
$persenRealisasi = ($jumlahRealisasi / $totalPagu) * 100;
$terminBelumPengesahan1 = 6667517342;
$terminBelumPengesahan2 = 6667517342;
$totalTerminBelumPengesahan = $terminBelumPengesahan1 + $terminBelumPengesahan2;
$totalEstimasiRealisasi = $jumlahRealisasi + $totalTerminBelumPengesahan;
$persenEstimasiRealisasi = ($totalEstimasiRealisasi / $totalPagu) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimasi Realisasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/jquery-3.6.0.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <style>
    body {
        display: flex;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #e4e4e4;
    }
    .main-content {
        flex-grow: 1;
        padding: 20px;
        background-color: #fff;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1); /* Shadow for separation */
    }
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
     <!-- Include Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<div class="container mt-4">
    <h2>Estimasi Realisasi</h2>
    <div class="row">
        <div class="col-6">
            <div class="mb-3">
                <label for="totalPagu" class="form-label">Total Pagu (a)</label>
                <input type="text" id="totalPagu" class="form-control" value="<?php echo number_format($totalPagu, 2, ',', '.'); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="jumlahRealisasi" class="form-label">Jumlah Realisasi Yang Sudah Disahkan (b)</label>
                <input type="text" id="jumlahRealisasi" class="form-control" value="<?php echo number_format($jumlahRealisasi, 2, ',', '.'); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="persenRealisasi" class="form-label">Persentase Realisasi</label>
                <input type="text" id="persenRealisasi" class="form-control" value="<?php echo number_format($persenRealisasi, 2, ',', '.'); ?>%" readonly>
            </div>
            <div class="mb-3">
                <label for="terminBelumPengesahan" class="form-label">Termin Pembayaran Belum Pengesahan:</label>
                <input type="text" id="terminBelumPengesahan1" class="form-control" value="<?php echo number_format($terminBelumPengesahan1, 2, ',', '.'); ?>" readonly>
                <input type="text" id="terminBelumPengesahan2" class="form-control" value="<?php echo number_format($terminBelumPengesahan2, 2, ',', '.'); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="totalTermin" class="form-label">Total Termin Belum Pengesahan (c)</label>
                <input type="text" id="totalTermin" class="form-control" value="<?php echo number_format($totalTerminBelumPengesahan, 2, ',', '.'); ?>" readonly>
            </div>
        </div>
        <div class="col-6">
            <div class="mb-3">
                <label for="totalEstimasiRealisasi" class="form-label">Total Estimasi Realisasi (b+c)</label>
                <input type="text" id="totalEstimasiRealisasi" class="form-control" value="<?php echo number_format($totalEstimasiRealisasi, 2, ',', '.'); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="persenEstimasiRealisasi" class="form-label">Total Estimasi Persentase Realisasi (b+c)/a</label>
                <input type="text" id="persenEstimasiRealisasi" class="form-control" value="<?php echo number_format($persenEstimasiRealisasi, 2, ',', '.'); ?>%" readonly>
            </div>
        </div>
    </div>
</div>

</body>
</html>
