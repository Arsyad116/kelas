<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Toko Online</title>
    <style>
        body {
            background: url('images/bgg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .logo-navbar {
            height: 65px;
            width: auto;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 50px 0;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #ffffff;
            font-size: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .social-icons a:hover {
            background: rgba(255, 255, 255, 0.5);
            color: #000;
        }

        .social-icons img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }

        .about-section {
            text-align: center;
            margin: 50px auto;
            max-width: 800px;
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
        }

        .about-section h2 {
            margin-bottom: 20px;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="bg-dark text-white">
    <nav class="container navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" alt="Logo" class="logo-navbar">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </nav>
</header>

<section class="about-section">
    <h2>Media Sosial</h2>
    <p>Ikuti kami di media sosial untuk mendapatkan informasi terbaru tentang produk dan promo menarik.</p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore quo, minima quas natus cum incidunt autem eaque deleniti cumque quidem debitis accusantium numquam corrupti illo praesentium dolores facere voluptatem sit.</p>
    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Odio illo ea commodi dicta velit quasi adipisci dolore sapiente quaerat voluptatibus. Eligendi reiciendis nulla est. Maxime accusantium pariatur iure sint placeat!</p>
</section>

<section class="social-icons">
    <a href="#"><img src="images/fb.png" alt="Facebook"> Facebook</a>
    <a href="#"><img src="images/ig.png" alt="Instagram"> Instagram</a>
    <a href="#"><img src="images/x.jpg" alt="Twitter"> Twitter</a>
    <a href="#"><img src="images/yt.jpg" alt="YouTube"> YouTube</a>
</section>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 Toko Online. Hak Cipta Dilindungi.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
