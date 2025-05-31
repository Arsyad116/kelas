<?php
// cart.php - Shopping Cart System
session_start();
require_once 'dbcontroller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DBController();
$user_id = $_SESSION['user_id'];

// Get customer info
$customer = $db->fetchOne("SELECT * FROM pelanggan WHERE id_user = ?", [$user_id]);

// Handle cart operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        $cart_id = $_POST['cart_id'];
        $quantity = $_POST['quantity'];
        
        if ($quantity > 0) {
            $db->execute("UPDATE cart SET jumlah = ? WHERE id_cart = ? AND id_user = ?", [$quantity, $cart_id, $user_id]);
        } else {
            $db->execute("DELETE FROM cart WHERE id_cart = ? AND id_user = ?", [$cart_id, $user_id]);
        }
        
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['remove_item'])) {
        $cart_id = $_POST['cart_id'];
        $db->execute("DELETE FROM cart WHERE id_cart = ? AND id_user = ?", [$cart_id, $user_id]);
        
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['clear_cart'])) {
        $db->execute("DELETE FROM cart WHERE id_user = ?", [$user_id]);
        
        header("Location: cart.php");
        exit();
    }
}

// Get cart items
$cart_items = $db->fetchAll("
    SELECT c.*, m.nama_menu, m.harga, m.gambar, m.deskripsi 
    FROM cart c 
    JOIN menu m ON c.id_menu = m.id_menu 
    WHERE c.id_user = ? 
    ORDER BY c.created_at DESC
", [$user_id]);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['harga'] * $item['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-item {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .cart-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 80px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🍽️ Restoran Nusantara</a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">← Kembali ke Menu</a>
                <a class="nav-link" href="histori.php">Histori</a>
                <a class="nav-link" href="login.php?logout=1">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h5>
                        <?php if (!empty($cart_items)): ?>
                            <form method="POST" style="display: inline;">
                                <button type="submit" name="clear_cart" class="btn btn-outline-danger btn-sm" 
                                        onclick="return confirm('Hapus semua item dari keranjang?')">
                                    <i class="fas fa-trash"></i> Kosongkan Keranjang
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($cart_items)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h4>Keranjang Kosong</h4>
                                <p class="text-muted">Belum ada item di keranjang Anda</p>
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-utensils"></i> Lihat Menu
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="upload/<?= $item['gambar'] ?: 'default.jpg' ?>" 
                                                 class="cart-image" 
                                                 alt="<?= htmlspecialchars($item['nama_menu']) ?>"
                                                 onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['nama_menu']) ?></h6>
                                            <p class="text-muted small mb-0"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                            <strong class="text-primary">Rp <?= number_format($item['harga'], 0, ',', '.') ?></strong>
                                        </div>
                                        <div class="col-md-3">
                                            <form method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="cart_id" value="<?= $item['id_cart'] ?>">
                                                <input type="number" name="quantity" value="<?= $item['jumlah'] ?>" 
                                                       min="1" max="10" class="form-control quantity-input me-2">
                                                <button type="submit" name="update_cart" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></strong>
                                        </div>
                                        <div class="col-md-1">
                                            <form method="POST">
                                                <input type="hidden" name="cart_id" value="<?= $item['id_cart'] ?>">
                                                <button type="submit" name="remove_item" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Hapus item ini dari keranjang?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-receipt"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Layanan</span>
                            <span>Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary">Rp <?= number_format($total, 0, ',', '.') ?></strong>
                        </div>
                        
                        <?php if (!empty($cart_items)): ?>
                            <a href="checkout.php" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-credit-card"></i> Checkout
                            </a>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus"></i> Tambah Menu
                        </a>
                    </div>
                </div>
                
                <!-- Customer Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6><i class="fas fa-user"></i> Informasi Pengiriman</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong><?= htmlspecialchars($customer['nama_pelanggan']) ?></strong></p>
                        <p class="mb-1 text-muted small"><?= htmlspecialchars($customer['alamat']) ?></p>
                        <p class="mb-0 text-muted small"><?= htmlspecialchars($customer['no_telp']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>