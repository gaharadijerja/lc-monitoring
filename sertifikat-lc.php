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

// Add Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_data'])) {
    $satker = $_POST['satker'];
    $nomor_lc = $_POST['nomor_lc'];
    $tanggal_lc = $_POST['tanggal_lc'];
    $no_kontrak = $_POST['no_kontrak'];
    $nilai = $_POST['nilai'];
    $mata_uang = $_POST['mata_uang'];

    // File upload handling
    $dokumen_path = '';
    if (isset($_FILES['dokumen_lc']) && $_FILES['dokumen_lc']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $filename = basename($_FILES['dokumen_lc']['name']);
        $dokumen_path = $upload_dir . time() . "_" . $filename;
        move_uploaded_file($_FILES['dokumen_lc']['tmp_name'], $dokumen_path);
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO lc_certificate (satker, nomor_lc, tanggal_lc, no_kontrak, nilai, mata_uang, dokumen_path, tanggal_insert, tanggal_update) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssssss", $satker, $nomor_lc, $tanggal_lc, $no_kontrak, $nilai, $mata_uang, $dokumen_path);
    $stmt->execute();
    $stmt->close();
}

// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch Data with limit and offset
$total_result = $conn->query("SELECT COUNT(*) AS total FROM lc_certificate")->fetch_assoc()['total'];
$total_pages = ceil($total_result / $limit);

$result = $conn->query("SELECT * FROM lc_certificate LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat L/C</title>
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
    .table 
    {
        margin: 20px;
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
    <div class="main-content container mt-4">
        <h1>Sertifikat L/C</h1>
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="satker" class="form-label">Satker</label>
                    <input type="text" class="form-control" id="satker" name="satker" required>
                </div>
                <div class="col-md-6">
                    <label for="nomor_lc" class="form-label">Nomor L/C</label>
                    <input type="text" class="form-control" id="nomor_lc" name="nomor_lc" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_lc" class="form-label">Tanggal L/C</label>
                    <input type="date" class="form-control" id="tanggal_lc" name="tanggal_lc" required>
                </div>
                <div class="col-md-6">
                    <label for="no_kontrak" class="form-label">No Kontrak</label>
                    <input type="text" class="form-control" id="no_kontrak" name="no_kontrak" required>
                </div>
                <div class="col-md-6">
                    <label for="nilai" class="form-label">Nilai L/C</label>
                    <input type="number" class="form-control" id="nilai" name="nilai" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label for="mata_uang" class="form-label">Mata Uang</label>
                    <select class="form-select" id="mata_uang" name="mata_uang" required>
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>                        
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="dokumen_lc" class="form-label">Dokumen Sertifikat L/C</label>
                    <input type="file" class="form-control" id="dokumen_lc" name="dokumen_lc">
                </div>
            </div>
            <button type="submit" name="add_data" class="btn btn-primary mt-3">Tambah Data</button>
        </form>

        <button class="btn btn-secondary mb-3" onclick="window.print();">Cetak</button>
        <!-- Tabel Data -->
        <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Satker</th>
                    <th>Nomor L/C</th>
                    <th>Tanggal L/C</th>
                    <th>No Kontrak</th>
                    <th>Nilai</th>
                    <th>Mata Uang</th>
                    <th>Dokumen</th>
                    <th>Tanggal Insert</th>
                    <th>Tanggal Update</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['satker']); ?></td>
                            <td><?= htmlspecialchars($row['nomor_lc']); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_lc']); ?></td>
                            <td><?= htmlspecialchars($row['no_kontrak']); ?></td>
                            <td><?= number_format($row['nilai'], 2); ?></td>
                            <td><?= htmlspecialchars($row['mata_uang']); ?></td>
                            <td>
                                <?php if (!empty($row['dokumen_path'])): ?>
                                    <a href="<?= htmlspecialchars($row['dokumen_path']); ?>" target="_blank">Download</a>
                                <?php else: ?>
                                    No file
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['tanggal_insert']); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_update']); ?></td>
                            <td>
                                <a href="update.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Update</a>
                                <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
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

