<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'koneksi.php';

// Handle deleting a user
if (isset($_POST['delete'])) {
    $idToDelete = $_POST['user_id'];
    $deleteQuery = $conn->prepare("DELETE FROM user WHERE id = ?");
    $deleteQuery->bind_param("i", $idToDelete);
    $deleteQuery->execute();
    $deleteQuery->close();
    header("Location: user-management.php");
    exit;
}

// Handle adding a new user
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $addStmt = $conn->prepare("INSERT INTO user (username, email, full_name, role, password) VALUES (?, ?, ?, ?, ?)");
    $addStmt->bind_param("sssss", $username, $email, $full_name, $role, $password);
    $addStmt->execute();
    $addStmt->close();
    header("Location: user-management.php");
    exit;
}

// Pagination setup
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total halaman
$total_result = $conn->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc()['total'];
$total_pages = ceil($total_result / $limit);

// Ambil data pengguna dengan batasan halaman
$query = "SELECT id, username, email, full_name, role FROM user LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Sistem Administrasi L/C</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        table {
            background: #fff;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Daftar User</h1>
        
        <!-- Add User Form -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="col">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="col">
                    <select name="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="satker">Satker</option>
                        <option value="bi">BI</option>
                        <option value="kppn_kph">KPPN KPH</option>
                        <option value="dja">DJA</option>
                    </select>
                </div>
                <div class="col">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="col">
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-secondary" onclick="window.print();">Print</button>
        </div>
        <!-- Tabel data -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>