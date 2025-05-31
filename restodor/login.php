<?php
// login.php - Login System
session_start();
require_once 'dbcontroller.php';

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DBController();
    
    if (isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = "Username dan password harus diisi!";
        } else {
            $user = $db->fetchOne("SELECT * FROM users WHERE username = ?", [$username]);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Username atau password salah!";
            }
        }
    }
    
    if (isset($_POST['register'])) {
        $username = trim($_POST['reg_username']);
        $password = $_POST['reg_password'];
        $confirm_password = $_POST['confirm_password'];
        $nama_pelanggan = trim($_POST['nama_pelanggan']);
        $alamat = trim($_POST['alamat']);
        $no_telp = trim($_POST['no_telp']);
        
        if (empty($username) || empty($password) || empty($nama_pelanggan) || empty($alamat) || empty($no_telp)) {
            $error = "Semua field harus diisi!";
        } elseif ($password !== $confirm_password) {
            $error = "Password konfirmasi tidak cocok!";
        } elseif (strlen($password) < 6) {
            $error = "Password minimal 6 karakter!";
        } else {
            // Check if username already exists
            $existing_user = $db->fetchOne("SELECT id_user FROM users WHERE username = ?", [$username]);
            
            if ($existing_user) {
                $error = "Username sudah digunakan!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Start transaction
                $db->getConnection()->begin_transaction();
                
                try {
                    // Insert user
                    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')");
                    $stmt->bind_param("ss", $username, $hashed_password);
                    $stmt->execute();
                    
                    $user_id = $db->getLastInsertId();
                    
                    // Insert pelanggan
                    $stmt = $db->prepare("INSERT INTO pelanggan (id_user, nama_pelanggan, alamat, no_telp) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $user_id, $nama_pelanggan, $alamat, $no_telp);
                    $stmt->execute();
                    
                    $db->getConnection()->commit();
                    $success = "Registrasi berhasil! Silakan login.";
                    
                } catch (Exception $e) {
                    $db->getConnection()->rollback();
                    $error = "Terjadi kesalahan saat registrasi!";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .nav-pills .nav-link {
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="text-center mb-4">
                <h1 class="text-white">🍽️ Restoran Nusantara</h1>
            </div>
            
            <div class="card">
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    
                    <!-- Navigation tabs -->
                    <ul class="nav nav-pills nav-justified mb-3" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="pill" data-bs-target="#register" type="button">Daftar</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="authTabsContent">
                        <!-- Login Form -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                            </form>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Demo Admin: admin / password<br>
                                    Demo Customer: customer1 / password
                                </small>
                            </div>
                        </div>
                        
                        <!-- Register Form -->
                        <div class="tab-pane fade" id="register" role="tabpanel">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="reg_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="reg_username" name="reg_username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="reg_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="reg_password" name="reg_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_pelanggan" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="no_telp" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                                </div>
                                <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>