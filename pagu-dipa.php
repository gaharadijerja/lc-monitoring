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

// Inisialisasi variabel filter
$filter_satker = isset($_GET['filter_satker']) ? $_GET['filter_satker'] : '';
$filter_akun = isset($_GET['filter_akun']) ? $_GET['filter_akun'] : '';
$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Hitung offset untuk pagination
$offset = ($current_page - 1) * $rows_per_page;

// Query dengan filter dan pagination
$sql = "SELECT * FROM monitoringdipa WHERE 1=1";
if (!empty($filter_satker)) {
    $sql .= " AND KodeSatker LIKE '%" . $conn->real_escape_string($filter_satker) . "%'";
}
if (!empty($filter_akun)) {
    $sql .= " AND Akun LIKE '%" . $conn->real_escape_string($filter_akun) . "%'";
}
$sql .= " LIMIT $offset, $rows_per_page";
$result = $conn->query($sql);

// Hitung total data untuk pagination
$total_sql = "SELECT COUNT(*) as total FROM monitoringdipa WHERE 1=1";
if (!empty($filter_satker)) {
    $total_sql .= " AND KodeSatker LIKE '%" . $conn->real_escape_string($filter_satker) . "%'";
}
if (!empty($filter_akun)) {
    $total_sql .= " AND Akun LIKE '%" . $conn->real_escape_string($filter_akun) . "%'";
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $rows_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring DIPA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container mt-4">
        <h2 class="mb-4">Monitoring DIPA</h2>
        <!-- Form Filter -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="filter_satker" class="form-label">Filter Kode Satker</label>
                    <input type="text" id="filter_satker" name="filter_satker" class="form-control" value="<?php echo htmlspecialchars($filter_satker); ?>">
                </div>
                <div class="col-md-3">
                    <label for="filter_akun" class="form-label">Filter Akun</label>
                    <input type="text" id="filter_akun" name="filter_akun" class="form-control" value="<?php echo htmlspecialchars($filter_akun); ?>">
                </div>
                <div class="col-md-3">
                    <label for="rows_per_page" class="form-label">Rows per Page</label>
                    <select id="rows_per_page" name="rows_per_page" class="form-control">
                        <option value="5" <?php echo $rows_per_page == 5 ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo $rows_per_page == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo $rows_per_page == 20 ? 'selected' : ''; ?>>20</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Terapkan</button>
                    <a href="pagu-dipa.php" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between mb-3">
                <button class="btn btn-secondary" id="printButton">Cetak</button>
        </div>
        <!-- Tabel Data -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Satker</th>
                        <th>Akun</th>
                        <th>Output</th>
                        <th>Dana</th>
                        <th>Pagu</th>
                        <th>Pencadangan</th>
                        <th>Realisasi</th>
                        <th colspan="3" class="text-center">Belum Pengesahan</th>
                        <th>Estimasi Sisa</th>
                    </tr>
                    <tr>
                        <th colspan="8"></th>
                        <th>Jumlah</th>
                        <th>Nilai</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = $offset + 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['KodeSatker'] . "</td>";
                        echo "<td>" . $row['Akun'] . "</td>";
                        echo "<td>" . $row['Output'] . "</td>";
                        echo "<td>" . $row['Dana'] . "</td>";
                        echo "<td>" . number_format($row['Pagu'], 0, ',', '.') . "</td>";
                        echo "<td>" . number_format($row['Pencadangan'], 0, ',', '.') . "</td>";
                        echo "<td>" . number_format($row['Realisasi'], 0, ',', '.') . "</td>";
                        echo "<td>" . $row['BelumPengesahan_Jumlah'] . "</td>";
                        echo "<td>" . number_format($row['BelumPengesahan_Nilai'], 0, ',', '.') . "</td>";
                        echo "<td></td>";
                        echo "<td>" . number_format($row['EstimasiSisa'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12' class='text-center'>No data available</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
               <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">Previous</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Next</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script>
        // Tambahkan event listener pada tombol Cetak
        document.getElementById('printButton').addEventListener('click', function () {
            window.print(); // Panggil fungsi cetak browser
        });
    </script>
</body>
</html>
