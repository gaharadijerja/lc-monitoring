<?php
session_start(); // Memulai sesi

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php?error=not_logged_in");
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Menangani tambah data
if (isset($_POST['tambah_data'])) {
    $nomor_top = $_POST['nomor_top'];
    $nomor_lc = $_POST['nomor_lc'];
    $tanggal_top = $_POST['tanggal_top'];
    $no_nodis_bi = $_POST['no_nodis_bi'];
    $dokumen_top = $_FILES['dokumen_top']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($dokumen_top);

    // Upload file
    move_uploaded_file($_FILES['dokumen_top']['tmp_name'], $target_file);

    $sql = "INSERT INTO term_of_payments (nomor_top, nomor_lc, tanggal_top, no_nodis_bi, dokumen_top) VALUES ('$nomor_top', '$nomor_lc', '$tanggal_top', '$no_nodis_bi', '$dokumen_top')";
    $conn->query($sql);
}

// Pagination setup
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total halaman
$total_result = $conn->query("SELECT COUNT(*) AS total FROM term_of_payments")->fetch_assoc()['total'];
$total_pages = ceil($total_result / $limit);

// Ambil data term of payments
$result = $conn->query("SELECT * FROM term_of_payments LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Term Of Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
    <div class="main-content container mt-4">
        <h1>Term Of Payments</h1>

        <!-- Form untuk input data -->
                <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nomor_top" class="form-label">Nomor Term Of Payments</label>
                    <input type="text" id="nomor_top" name="nomor_top" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="nomor_lc" class="form-label">Nomor L/C</label>
                    <input type="text" id="nomor_lc" name="nomor_lc" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_top" class="form-label">Tanggal Term Of Payments</label>
                    <input type="date" id="tanggal_top" name="tanggal_top" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="no_nodis_bi" class="form-label">No Nodis Dari BI</label>
                    <input type="text" id="no_nodis_bi" name="no_nodis_bi" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="dokumen_top" class="form-label">Dokumen Term Of Payment</label>
                    <input type="file" id="dokumen_top" name="dokumen_top" class="form-control" required>
                </div>
            </div>
                    <button type="submit" name="tambah_data" class="btn btn-primary mt-3">Tambah Data</button>
                
            
        </form>


        <!-- Tabel untuk menampilkan data Term Of Payments -->
        <h3>List Term Of Payments</h3>
        <div class="d-flex justify-content-between mb-3">
             <button class="btn btn-secondary" onclick="window.print();">Print</button>
        </div>
        <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Term Of Payments</th>
                    <th>Nomor L/C</th>
                    <th>Tanggal Term Of Payments</th>
                    <th>No Nodis Dari BI</th>
                    <th>Dokumen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$counter}</td>
                            <td>{$row['nomor_top']}</td>
                            <td>{$row['nomor_lc']}</td>
                            <td>{$row['tanggal_top']}</td>
                            <td>{$row['no_nodis_bi']}</td>
                            <td><a href='uploads/{$row['dokumen_top']}' target='_blank'>Download</a></td>
                            <td>
                                <a href='update_top.php?id={$row['id']}' class='btn btn-warning btn-sm'>Update</a>
                                <a href='delete_top.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Tidak ada data term of payments</td></tr>";
                }
                ?>
            </tbody>
        </table>
        </div>
        <!-- Pagination -->
<nav>
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a>
            </li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1; ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
    </div>
</body>
</html>

<?php
$conn->close();
?>