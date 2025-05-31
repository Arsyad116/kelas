<?php
// index.php - Customer Main Page
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

// Get categories
$categories = $db->fetchAll("SELECT * FROM kategori ORDER BY nama_kategori");

// Get menu items with optional category filter
$category_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
if ($category_filter) {
    $menu_items = $db->fetchAll("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori WHERE m.status = 'aktif' AND m.id_kategori = ? ORDER BY m.nama_menu", [$category_filter]);
} else {
    $menu_items = $db->fetchAll("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori WHERE m.status = 'aktif' ORDER BY m.nama_menu");
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $menu_id = $_POST['menu_id'];
    $quantity = $_POST['quantity'];
    
    // Check if item already in cart
    $existing_item = $db->fetchOne("SELECT * FROM cart WHERE id_user = ? AND id_menu = ?", [$user_id, $menu_id]);
    
    if ($existing_item) {
        // Update quantity
        $new_quantity = $existing_item['jumlah'] + $quantity;
        $db->execute("UPDATE cart SET jumlah = ? WHERE id_cart = ?", [$new_quantity, $existing_item['id_cart']]);
    } else {
        // Insert new item
        $db->execute("INSERT INTO cart (id_user, id_menu, jumlah) VALUES (?, ?, ?)", [$user_id, $menu_id, $quantity]);
    }
    
    header("Location: index.php?success=1");
    exit();
}

// Get cart count
$cart_count = $db->fetchOne("SELECT COUNT(*) as count FROM cart WHERE id_user = ?", [$user_id])['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Nusantara - Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .menu-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .menu-card:hover {
            transform: translateY(-5px);
        }
        .menu-image {
            height: 200px;
            object-fit: cover;
        }
        .category-filter {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">🍽️ Restoran Nusantara</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="histori.php">Histori Pesanan</a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart"></i> Keranjang 
                        <?php if ($cart_count > 0): ?>
                            <span class="badge bg-danger"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($customer['nama_pelanggan']) ?>
                    </a>
                    <a class="nav-link" href="login.php?logout=1">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="text-center">
            <h1 class="display-4 fw-bold">Selamat Datang di Restoran Nusantara</h1>
            <p class="lead">Nikmati kelezatan masakan tradisional Indonesia</p>
        </div>
    </section>

    <div class="container my-5">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Menu berhasil ditambahkan ke keranjang!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Category Filter -->
        <div class="category-filter">
            <h5>Filter Kategori:</h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php" class="btn <?= !$category_filter ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Semua Menu
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="index.php?kategori=<?= $category['id_kategori'] ?>" 
                       class="btn <?= $category_filter == $category['id_kategori'] ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <?= htmlspecialchars($category['nama_kategori']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Menu Grid -->
        <div class="row">
            <?php if (empty($menu_items)): ?>
                <div class="col-12 text-center">
                    <h3>Tidak ada menu yang tersedia</h3>
                </div>
            <?php else: ?>
                <?php foreach ($menu_items as $item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card menu-card shadow-sm">
                            <img src="upload/<?= $item['gambar'] ?: 'default.jpg' ?>" 
                                 class="card-img-top menu-image" 
                                 alt="<?= htmlspecialchars($item['nama_menu']) ?>"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($item['nama_menu']) ?></h5>
                                <p class="card-text text-muted small"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                <p class="card-text"><small class="text-muted"><?= htmlspecialchars($item['nama_kategori']) ?></small></p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="text-primary mb-0">Rp <?= number_format($item['harga'], 0, ',', '.') ?></h5>
                                    </div>
                                    
                                    <form method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="menu_id" value="<?= $item['id_menu'] ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="10" class="form-control form-control-sm" style="width: 70px;">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm flex-grow-1">
                                            <i class="fas fa-cart-plus"></i> Tambah
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 Restoran Nusantara. All rights reserved.</p>