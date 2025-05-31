<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Daftar produk yang akan ditambahkan
$products = [
    [
        'nama' => 'PUMA Palermo I',
        'harga' => 876000,
        'gambar' => 'black.jpeg',
        'stok' => 10,
        'kategori' => 'Sepatu',
        'is_active' => 1
    ],
    [
        'nama' => 'PUMA Palermo II',
        'harga' => 680000,
        'gambar' => 'brown.jpeg',
        'stok' => 15,
        'kategori' => 'Sepatu',
        'is_active' => 1
    ],
    [
        'nama' => 'PUMA Palermo III',
        'harga' => 750000,
        'gambar' => 'green.jpeg',
        'stok' => 8,
        'kategori' => 'Sepatu',
        'is_active' => 1
    ],
    [
        'nama' => 'PUMA Suede Classic',
        'harga' => 950000,
        'gambar' => 'black.jpeg', // Menggunakan gambar yang sudah ada
        'stok' => 12,
        'kategori' => 'Sepatu',
        'is_active' => 1
    ],
    [
        'nama' => 'PUMA RS-X',
        'harga' => 1200000,
        'gambar' => 'brown.jpeg', // Menggunakan gambar yang sudah ada
        'stok' => 7,
        'kategori' => 'Sepatu',
        'is_active' => 1
    ],
    [
        'nama' => 'PUMA T-Shirt Basic',
        'harga' => 350000,
        'gambar' => 'green.jpeg', // Menggunakan gambar yang sudah ada
        'stok' => 20,
        'kategori' => 'Pakaian',
        'is_active' => 1
    ]
];

// Hapus data produk yang ada (opsional)
$conn->query("TRUNCATE TABLE produk");

// Tambahkan produk ke database
$success = 0;
$errors = [];

foreach ($products as $product) {
    $sql = "INSERT INTO produk (nama, harga, gambar, stok, kategori, is_active)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssi",
        $product['nama'],
        $product['harga'],
        $product['gambar'],
        $product['stok'],
        $product['kategori'],
        $product['is_active']
    );

    if ($stmt->execute()) {
        $success++;
    } else {
        $errors[] = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Tutup koneksi
$conn->close();

// Tampilkan hasil dengan styling yang lebih baik
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - PUMA Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 40px 0;
        }
        .container {
            max-width: 800px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-header {
            background-color: #e50010;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #e50010;
            border-color: #e50010;
        }
        .btn-primary:hover {
            background-color: #c5000e;
            border-color: #c5000e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-database me-2"></i> Hasil Penambahan Produk</h3>
            </div>
            <div class="card-body text-center py-5">
                <?php if ($success > 0): ?>
                    <i class="fas fa-check-circle success-icon"></i>
                    <h4>Berhasil!</h4>
                    <p class="lead">Berhasil menambahkan <?php echo $success; ?> produk dari <?php echo count($products); ?> produk.</p>
                <?php else: ?>
                    <i class="fas fa-times-circle error-icon"></i>
                    <h4>Gagal!</h4>
                    <p class="lead">Tidak ada produk yang berhasil ditambahkan.</p>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-4">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i> Error:</h5>
                        <ul class="list-group list-group-flush text-start">
                            <?php foreach ($errors as $error): ?>
                                <li class="list-group-item bg-transparent"><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="/" class="btn btn-primary"><i class="fas fa-home me-2"></i> Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
