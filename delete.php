<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lc';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan `id` adalah integer untuk menghindari SQL injection
    $stmt = $conn->prepare("DELETE FROM lc_certificate WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Data berhasil dihapus!";
    } else {
        echo "Gagal menghapus data: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "ID tidak ditemukan.";
}
$conn->close();
?>
