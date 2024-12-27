<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Administrasi L/C</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
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
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .logout-btn {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            background-color: #dc3545;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #b02a37;
        }
        .alert {
            margin-top: 20px;
        }        
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <?php
    // Mulai sesi
    session_start();

    // Masukkan file koneksi database
    include 'koneksi.php';

    $success_message = "";
    $error_message = "";

    // Proses formulir saat dikirim
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'];

        if ($password !== $confirm_password) {
            $error_message = "Password dan konfirmasi password tidak sesuai.";
        } else {
            // Periksa apakah username atau email sudah ada
            $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "Username atau email sudah digunakan.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Simpan data ke database
                $stmt = $conn->prepare("INSERT INTO user (username, email, full_name, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $email, $full_name, $hashed_password, $role);

                if ($stmt->execute()) {
                    $success_message = "Registrasi berhasil.";
                } else {
                    $error_message = "Registrasi gagal. Silakan coba lagi.";
                }
            }
            $stmt->close();
        }
    }
    ?>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Main Content -->
    <div class="content">
        <div class="form-container">
            <h1 class="text-center">Register</h1>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success_message); ?>
                </div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="satker">Satker</option>
                        <option value="bi">BI</option>
                        <option value="kppn_kph">KPPN KPH</option>
                        <option value="dja">DJA</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                Already have an account? <a href="login.php">Login here</a>.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
