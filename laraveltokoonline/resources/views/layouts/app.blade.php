<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PUMA Store') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Scripts -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        // Set CSRF token for all AJAX requests
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

    <style>
        body {
            background: url('{{ asset('images/bgg.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #000000;
        }
        #bannerCarousel {
            width: 80%;
            height: 400px;
            margin-top: 20px;
        }
        #bannerCarousel .carousel-inner img {
            object-fit: cover;
            height: 100%;
        }
        .auth-buttons .btn {
            margin-left: 10px;
            border-radius: 20px;
        }
        .logo-navbar {
            height: 65px;
            width: auto;
        }
        .search-container {
            position: relative;
            width: 250px;
        }
        .search-container input {
            width: 100%;
            padding: 8px 30px 8px 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }
        .search-container button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
        }
        .modal-content {
            color: #000;
        }
        /* General Modal Styles */
        .modal-content.bg-dark {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }

        .modal-header.border-secondary {
            border-bottom: 1px solid #424242;
        }

        .close.text-light {
            color: #e0e0e0;
            opacity: 1;
        }

        .form-control.bg-secondary {
            background-color: #212121;
            border: 1px solid #424242;
            color: #e0e0e0;
        }

        .form-control.bg-secondary:focus {
            border-color: #424242;
            box-shadow: 0 0 5px rgba(187, 134, 252, 0.5);
        }

        .btn-primary {
            background-color: #424242;
            border: none;
        }

        .btn-primary:hover {
            background-color: #424242;
        }

        /* Notification Styles */
        .notification {
            display: none;
            padding: 0.75rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-top: 10px;
            text-align: center;
        }

        .notification.success {
            display: block;
            background-color: #4caf50;
            color: #ffffff;
            border: 1px solid #43a047;
        }

        .notification.error {
            display: block;
            background-color: #f44336;
            color: #ffffff;
            border: 1px solid #e53935;
        }
        /* General Card Styles */
        /* Styling dasar untuk card */
        .card {
        transition: all 0.3s ease; /* Animasi halus saat hover */
        border: none; /* Menghilangkan border default */
        }

        /* Efek saat card di-hover */
        .card:hover {
        box-shadow:
            10px 0 15px -5px rgba(0, 0, 0, 0.2), /* Bayangan di sisi kanan */
            -10px 0 15px -5px rgba(0, 0, 0, 0.2); /* Bayangan di sisi kiri */
        transform: scale(1.05); /* Memperbesar card sedikit */
        border: 2px solid #ccc; /* Tambahkan border abu-abu */
        }

        /* Efek tambahan untuk gambar di dalam card */
        .card-img-top {
        transition: all 0.3s ease;
        }

        .card:hover .card-img-top {
        opacity: 0.9; /* Mengurangi opacity gambar saat hover */
        }

        /* Styling tombol Add to Cart */
        .add-to-cart {
        margin-top: 10px; /* Jarak antara harga dan tombol */
        font-size: 1rem; /* Ukuran font tombol */
        padding: 10px; /* Padding tombol */
        background-color:rgb(80, 80, 80); /* Warna biru default */
        border: none; /* Hilangkan border */
        border-radius: 5px; /* Sudut melengkung */
        transition: all 0.3s ease; /* Animasi halus */
        }

        /* Efek hover pada tombol Add to Cart */
        .add-to-cart:hover {
        background-color:rgb(56, 60, 63); /* Warna lebih gelap saat hover */
        transform: scale(1.05); /* Memperbesar tombol sedikit */
        }
        .cart-count {
            font-size: 0.7em;
            transform: translate(-50%, -50%);
            margin-left: 35px;
            margin-top: -5px;
        }

        .btn-light:hover {
            background-color: #e2e6ea;
        }

        .fa-shopping-cart {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
