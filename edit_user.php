<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'koneksi.php';

// Check if an ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id'];

// Fetch user data by ID
$stmt = $conn->prepare("SELECT id, username, email, full_name, role FROM user WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_users.php");
    exit();
}

$user = $result->fetch_assoc();

// Update user data if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];

    // Prepare and execute the update statement
    $updateStmt = $conn->prepare("UPDATE user SET username = ?, email = ?, full_name = ?, role = ? WHERE id = ?");
    $updateStmt->bind_param("ssssi", $username, $email, $full_name, $role, $userId);
    
    if ($updateStmt->execute()) {
        header("Location: manage_users.php");
        exit();
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
    <title>Edit User</title>
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
        <h1>Edit User</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="satker" <?= $user['role'] === 'satker' ? 'selected' : ''; ?>>Satker</option>
                    <option value="bi" <?= $user['role'] === 'bi' ? 'selected' : ''; ?>>BI</option>
                    <option value="kppn_kph" <?= $user['role'] === 'kppn_kph' ? 'selected' : ''; ?>>KPPN KPH</option>
                    <option value="dja" <?= $user['role'] === 'dja' ? 'selected' : ''; ?>>DJA</option>
                    <!-- Add more roles if needed -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>