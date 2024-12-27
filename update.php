<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lc';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ID');
}

$id = $_GET['id'];

// Fetch data from the database
$stmt = $conn->prepare("SELECT * FROM lc_certificate WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die('Data not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_data'])) {
    // Get the updated data from the form
    $satker = $_POST['satker'];
    $nomor_lc = $_POST['nomor_lc'];
    $tanggal_lc = $_POST['tanggal_lc'];
    $no_kontrak = $_POST['no_kontrak'];
    $nilai = $_POST['nilai'];
    $mata_uang = $_POST['mata_uang'];

    // Handle file upload
    $dokumen_path = $row['dokumen_path']; // Keep the original file if no new file is uploaded
    if (isset($_FILES['dokumen_lc']) && $_FILES['dokumen_lc']['error'] == 0) {
        $upload_dir = 'uploads/';
        $dokumen_path = $upload_dir . basename($_FILES['dokumen_lc']['name']);
        $target_file = $target_dir . $dokumen_top;
        move_uploaded_file($_FILES['dokumen_lc']['tmp_name'], $dokumen_path);
    }

    // Update the data in the database
    $update_stmt = $conn->prepare("UPDATE lc_certificate SET satker = ?, nomor_lc = ?, tanggal_lc = ?, no_kontrak = ?, nilai = ?, mata_uang = ?, dokumen_path = ? WHERE id = ?");
    $update_stmt->bind_param("sssssssi", $satker, $nomor_lc, $tanggal_lc, $no_kontrak, $nilai, $mata_uang, $dokumen_path, $id);
    $update_stmt->execute();
    $update_stmt->close();

    // Redirect to the same page after successful update
    header("Location: sertifikat-lc.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Sertifikat L/C</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            color: #fff;
            padding: 20px;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .sidebar a:hover {
            color: #fff;
            background-color: #495057;
            padding: 10px;
            border-radius: 5px;
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
    <div class="container mt-5">
        <h1>Update Sertifikat L/C</h1>
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="satker" class="form-label">Satker</label>
                    <input type="text" class="form-control" id="satker" name="satker" value="<?= htmlspecialchars($row['satker']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="nomor_lc" class="form-label">Nomor L/C</label>
                    <input type="text" class="form-control" id="nomor_lc" name="nomor_lc" value="<?= htmlspecialchars($row['nomor_lc']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_lc" class="form-label">Tanggal L/C</label>
                    <input type="date" class="form-control" id="tanggal_lc" name="tanggal_lc" value="<?= htmlspecialchars($row['tanggal_lc']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="no_kontrak" class="form-label">No Kontrak</label>
                    <input type="text" class="form-control" id="no_kontrak" name="no_kontrak" value="<?= htmlspecialchars($row['no_kontrak']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="nilai" class="form-label">Nilai L/C</label>
                    <input type="number" class="form-control" id="nilai" name="nilai" value="<?= number_format($row['nilai'], 2); ?>" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label for="mata_uang" class="form-label">Mata Uang</label>
                    <select class="form-select" id="mata_uang" name="mata_uang" required>
                        <option value="USD" <?= $row['mata_uang'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                        <option value="EUR" <?= $row['mata_uang'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        <option value="IDR" <?= $row['mata_uang'] === 'IDR' ? 'selected' : ''; ?>>IDR</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="dokumen_lc" class="form-label">Dokumen Sertifikat L/C (Opsional)</label>
                    <input type="file" class="form-control" id="dokumen_lc" name="dokumen_lc">
                    <small class="form-text text-muted">Leave blank if not updating the document.</small>
                </div>
            </div>
            <button type="submit" name="update_data" class="btn btn-primary mt-3">Update Data</button>
            <a href="sertifikat-lc.php" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
