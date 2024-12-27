<?php
// Konfigurasi database
$host = "localhost";    // Nama host (biasanya localhost)
$username = "root";     // Username database
$password = "";         // Password database (kosong jika default di XAMPP)
$database = "lc"; // Nama database

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

//echo "Koneksi berhasil!"; 
?>
