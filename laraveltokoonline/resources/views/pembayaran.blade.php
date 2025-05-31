<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Pembayaran - Toko Online</title>
    <style>
        body {
            background: url('images/bgg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .payment-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .payment-container p {
            text-align: center;
            margin-bottom: 20px;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .payment-methods img {
            width: 80px;
            margin: 10px 0;
        }

        .btn-payment {
            width: 100%;
            background: #28a745;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            transition: 0.3s;
        }

        .btn-payment:hover {
            background: #218838;
        }

        /* Styling header agar lebih rapi dan elegan */
        header {
            background: rgba(0, 0, 0, 0.9);
            padding: 15px 0;
        }

        .navbar {
            padding: 0;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .navbar-nav .nav-link {
            color: #ffffff;
            padding: 10px 15px;
            transition: 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #f8c471;
        }
    </style>
</head>
<body>
<header>
    <nav class="container navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.html">Produk</a></li>
            </ul>
        </div>
    </nav>
</header>

<section class="payment-container">
    <h2>Metode Pembayaran</h2>
                     <p>               Metode Pembayaran yang tersedia untuk kenyamanan Anda.</p>
             
    <div class="payment-methods">
        <img src="images/visa.png" alt="Visa">
        <img src="images/mc.png" alt="Mastercard">
        <img src="images/pp.jpg" alt="PayPal">
        <img src="images/gp.jpeg" alt="GoPay">
        <img src="images/ovo.jpg" alt="OVO">
    </div>

</section>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 PUMA. Hak Cipta Dilindungi.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
