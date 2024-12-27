<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=not_logged_in");
    exit();
}

// Connect to the database
include 'koneksi.php';

// Check if an ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: your_main_page.php"); // Redirect to main page if no ID is provided
    exit();
}

$id = $_GET['id'];

// Fetch the existing data for the term of payment
$stmt = $conn->prepare("SELECT * FROM term_of_payments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data not found.";
    exit();
}

$data = $result->fetch_assoc();

// Handle the form submission to update the data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_top = $_POST['nomor_top'];
    $nomor_lc = $_POST['nomor_lc'];
    $tanggal_top = $_POST['tanggal_top'];
    $no_nodis_bi = $_POST['no_nodis_bi'];
    $dokumen_top = $data['dokumen_top']; // Default to existing document

    // Handle file upload if a new file is provided
    if (!empty($_FILES['dokumen_top']['name'])) {
        $target_dir = "uploads/";
        $dokumen_top = basename($_FILES['dokumen_top']['name']);
        $target_file = $target_dir . $dokumen_top;
        move_uploaded_file($_FILES['dokumen_top']['tmp_name'], $target_file);
    }

    // Update the database
    $updateStmt = $conn->prepare("UPDATE term_of_payments SET nomor_top = ?, nomor_lc = ?, tanggal_top = ?, no_nodis_bi = ?, dokumen_top = ? WHERE id = ?");
    $updateStmt->bind_param("sssssi", $nomor_top, $nomor_lc, $tanggal_top, $no_nodis_bi, $dokumen_top, $id);
    
    if ($updateStmt->execute()) {
        echo "<script>alert('Data updated successfully'); window.location.href='term-of-payment.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Term Of Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
    body {
        display: flex;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #e4e4e4;
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
        <h1>Update Term Of Payment</h1>
        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label for="nomor_top" class="form-label">Nomor Term Of Payments</label>
                <input type="text" id="nomor_top" name="nomor_top" class="form-control" value="<?= htmlspecialchars($data['nomor_top']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="nomor_lc" class="form-label">Nomor L/C</label>
                <input type="text" id="nomor_lc" name="nomor_lc" class="form-control" value="<?= htmlspecialchars($data['nomor_lc']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_top" class="form-label">Tanggal Term Of Payments</label>
                <input type="date" id="tanggal_top" name="tanggal_top" class="form-control" value="<?= htmlspecialchars($data['tanggal_top']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="no_nodis_bi" class="form-label">No Nodis Dari BI</label>
                <input type="text" id="no_nodis_bi" name="no_nodis_bi" class="form-control" value="<?= htmlspecialchars($data['no_nodis_bi']); ?>">
            </div>
            <div class="mb-3">
                <label for="dokumen_top" class="form-label">Dokumen Term Of Payment (upload to change)</label>
                <input type="file" id="dokumen_top" name="dokumen_top" class="form-control">
                <p>Current document: <a href="uploads/<?= htmlspecialchars($data['dokumen_top']); ?>" target="_blank"><?= htmlspecialchars($data['dokumen_top']); ?></a></p>
            </div>
            <button type="submit" class="btn btn-primary">Update Data</button>
            <a href="term-of-payment.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

   
</body>
</html>

<?php
$conn->close();
?>