<?php
session_start();

$error = '';
//if (isset($_GET['error']) && $_GET['error'] === 'not_logged_in') {
  //  $error = 'Silakan login terlebih dahulu untuk mengakses halaman ini.';
//}
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = rand(1000, 9999);
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha_input = $_POST['captcha'];

    // Validasi captcha
    if ($captcha_input != $_SESSION['captcha_code']) {
        $error = "Captcha tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: sertifikat-lc.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
// Reset Captcha
$_SESSION['captcha_code'] = rand(1000, 9999);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar { margin-bottom: 20px; 
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .container { margin-top: 100px; }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container">
        <h1 class="text-center mb-4">Login</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            Show
                        </button>
                        </div>
                        <div class="mb-3">
    <label for="captcha" class="form-label">Captcha</label>
    <div class="input-group">
        <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Masukkan kode captcha" required>
        <span class="input-group-text">
            <?= $_SESSION['captcha_code']; ?>
        </span>
    </div>
</div>

                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div class="register-link text-center">
                        <p>Tambah Akun? <a href="register.php">Klik di sini</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const toggleButton = this;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = 'Show';
        }
    });
</script>
</body>
</html>