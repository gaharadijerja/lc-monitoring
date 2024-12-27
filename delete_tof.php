<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lc';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Mengambil data yang akan dihapus untuk memastikan file dihapus juga
    $result = $conn->query("SELECT * FROM term_of_payments WHERE id = $id");
    $data = $result->fetch_assoc();

    // Menghapus file yang terkait
    $file_to_delete = "uploads/" . $data['dokumen_top'];
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete); // Menghapus file dari server
    }

    // Menghapus data dari database
    $sql = "DELETE FROM term_of_payments WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: term-of-payment.php"); // Redirect kembali setelah data dihapus
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
