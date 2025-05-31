<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Checkout</title>
    <script>
        function loadCart() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let cartList = document.getElementById('cart-list');
            let total = 0;

            cartList.innerHTML = '';
            cart.forEach((item, index) => {
                let subtotal = item.price * item.quantity;
                total += subtotal;
                cartList.innerHTML += `
                    <tr>
                        <td>${item.name}</td>
                        <td>Rp ${item.price.toLocaleString()}</td>
                        <td>${item.quantity}</td>
                        <td>Rp ${subtotal.toLocaleString()}</td>
                        <td><button class="btn btn-danger btn-sm" onclick="removeItem(${index})">Hapus</button></td>
                    </tr>`;
            });

            document.getElementById('total-price').innerText = 'Rp ' + total.toLocaleString();
        }

        function removeItem(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

        function checkout() {
            Toastify({
                text: 'Selamat! Pesanan Anda telah diproses!',
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#4caf50",
            }).showToast();
            localStorage.removeItem('cart');
            window.location.reload();
            window.location.href = 'produk.php';
        }

        document.addEventListener("DOMContentLoaded", loadCart);
    </script>
</head>
<body>
<header class="bg-dark text-white">
    <nav class="container navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.html">Toko Online</a>
    </nav>
</header>

<div class="container my-4">
    <h2>Checkout</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="cart-list"></tbody>
    </table>
    <h4>Total: <span id="total-price">Rp 0</span></h4>
    <button class="btn btn-success" onclick="checkout()">Bayar Sekarang</button>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 Toko Online</p>
</footer>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>
