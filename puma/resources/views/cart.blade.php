<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Keranjang Belanja - Puma Store</title>
    <style>
        /* Previous CSS styles remain the same */
        body {
            background: linear-gradient(135deg, #ffffff, #d9d9d9); /* Subtle light gradient */
            color: #333;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
        }

        .cart-container {
            background: linear-gradient(135deg, #333, #666); /* Darker gradient for elegance */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding: 40px;
            color: #fff;
            max-width: 800px;
            width: 90%;
            margin-top: 15px;
            margin-left: 150px;
        }

        .cart-header {
            background: linear-gradient(to right, #000, #CC0033); /* Eye-catching gradient */
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 15px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #444, #666); /* Sleek gradient for items */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .cart-item img {
            max-width: 120px;
            margin-right: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .total-section {
            background: linear-gradient(135deg, #555, #777); /* Smooth gradient for totals */
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
            color: white;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
        }

        .checkout-btn {
            background: linear-gradient(to right, #CC0033, #FF0000); /* Bold and inviting button */
            border: none;
            color: white;
            font-size: 1.1em;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .checkout-btn:hover {
            background: linear-gradient(to right, #A30029, #CC0033); /* Darker hover effect */
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
        }

    </style>
</head>
<body>
<!-- Header and navigation remain the same -->
<header class="bg-dark text-white">
    <nav class="container navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" alt="Logo" style="height: 65px; width: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
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

<div class="container">
    <div class="cart-container">
        <div class="cart-header">
            <h2 class="text-center mb-0">Keranjang Belanja</h2>
        </div>

        <div id="database-error" class="alert alert-danger d-none mt-3">
            <p><strong>Error:</strong> Terjadi masalah dengan kolom 'quantity' di database. <a href="fix_quantity.php" class="alert-link">Klik di sini untuk memperbaiki</a>.</p>
        </div>

        <div id="cart-items">
            <!-- Cart items will be dynamically added here -->
        </div>

        <div class="total-section">
            <div class="row">
                <div class="col-md-6">
                    <h4 style="font-weight: bold;">Total Harga</h4>
                </div>
                <div class="col-md-6 text-right">
                    <h4 id="total-price" style="font-weight: bold;">Rp 0</h4>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="fix_quantity.php" class="btn btn-outline-secondary" title="Perbaiki database jika ada masalah">
                    <i class="fas fa-database mr-1"></i> Fix Database
                </a>
                <button id="checkout-btn" class="btn checkout-btn text-white" onclick="checkout()">Checkout</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer remains the same -->
<footer class="bg-dark text-white text-center py-3 mt-4">
    <nav>
        <ul class="list-inline">
            <li class="list-inline-item"><a class="text-white" href="index.html">Menu</a></li>
            <li class="list-inline-item"><a class="text-white" href="pembayaran.html">Pembayaran</a></li>
            <li class="list-inline-item"><a class="text-white" href="medsos.html">Medsos</a></li>
            <li class="list-inline-item"><a class="text-white" href="kontak.html">Kontak</a></li>
        </ul>
    </nav>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();

    // Check if there's a success parameter in the URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('checkout_success')) {
        const orderId = urlParams.get('order_id') || 'Unknown';

        // Show success notification
        Toastify({
            text: `Pesanan #${orderId} berhasil dicheckout! Barang telah dihapus dari keranjang.`,
            duration: 8000,
            gravity: "top",
            position: "center",
            backgroundColor: "#4caf50",
            stopOnFocus: true,
            onClick: function(){} // Prevents auto-dismiss when clicked
        }).showToast();
    }
});

function loadCartItems() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsContainer = document.getElementById('cart-items');
    cartItemsContainer.innerHTML = ''; // Clear existing items

    cart.forEach((item, index) => {
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="flex-grow-1">
                <h5>${item.name}</h5>
                <p>Harga: Rp ${parseFloat(item.price).toLocaleString()}</p>
                <p>Quantity: ${item.quantity || 1}</p>
            </div>
            <button class="btn btn-danger" onclick="removeItem(${index})">Hapus</button>
        `;
        cartItemsContainer.appendChild(cartItem);
    });

    updateTotal();
}

function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCartItems();
    updateTotalPrice();
    updateCartCount();
    Toastify({
        text: 'Item berhasil dihapus dari keranjang!',
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "#4caf50"
    }).showToast();
}

function updateTotal() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let total = cart.reduce((sum, item) => {
        return sum + (parseFloat(item.price) * (item.quantity || 1));
    }, 0);
    document.getElementById('total-price').textContent = `Rp ${total.toLocaleString()}`;
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);

    // Update any cart count badges in the navbar
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = cartCount;
        element.style.display = cartCount > 0 ? 'block' : 'none';
    });

    // If cart is empty, show empty cart message
    if (cart.length === 0) {
        const cartItemsContainer = document.getElementById('cart-items');
        if (cartItemsContainer) {
            cartItemsContainer.innerHTML = `
                <div class="text-center p-4">
                    <h4 class="mb-3">Keranjang Belanja Kosong</h4>
                    <p>Silakan tambahkan produk ke keranjang Anda.</p>
                    <a href="index.php" class="btn btn-primary mt-3">Belanja Sekarang</a>
                </div>
            `;
        }
    }
}

function checkout() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) {
        Toastify({
            text: 'Keranjang anda kosong!',
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#f44336",
        }).showToast();
        return;
    }

    // Add user confirmation
    if (!confirm('Apakah Anda yakin ingin melakukan checkout?')) {
        return;
    }

    // Show loading indicator
    document.getElementById('checkout-btn').disabled = true;
    document.getElementById('checkout-btn').innerHTML = 'Memproses...';

    // Prepare order data
    const orderData = {
        items: cart.map(item => ({
            nama_produk: item.name,
            harga: parseFloat(item.price),
            quantity: item.quantity || 1,
            image: item.image || ''
        })),
        total: cart.reduce((sum, item) => sum + (parseFloat(item.price) * (item.quantity || 1)), 0)
    };

    console.log('Sending checkout data:', orderData);

    // Send to server
    console.log('Sending to URL:', window.location.origin + '/proses_checkout.php');
    fetch(window.location.origin + '/proses_checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        // Clone the response so we can log it and still use it
        const responseClone = response.clone();
        responseClone.text().then(text => {
            console.log('Raw response:', text);
        });

        return response.json();
    })
    .then(data => {
        console.log('Checkout response:', data);
        if (data.success) {
            // Log verification results
            if (data.verification) {
                console.log('Order verification:', data.verification);
            }

            // Clear cart
            localStorage.removeItem('cart');

            // Show success message with animation
            document.getElementById('cart-items').innerHTML = `
                <div class="text-center p-4 checkout-success-message">
                    <div class="checkout-success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4 class="mb-3">Terima kasih sudah berbelanja!</h4>
                    <p>Pesanan Anda telah berhasil dicheckout.</p>
                    <p class="order-id">Order ID: ${data.order_id}</p>
                    <p class="mt-3">Barang telah dihapus dari keranjang Anda.</p>
                    <a href="index.php" class="btn btn-primary mt-3">Kembali Berbelanja</a>
                </div>
            `;

            // Add CSS for animation
            const style = document.createElement('style');
            style.textContent = `
                .checkout-success-message {
                    animation: fadeInUp 0.5s ease-out forwards;
                    background: linear-gradient(135deg, #f8f9fa, #ffffff);
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                    padding: 30px;
                }

                .checkout-success-icon {
                    font-size: 5rem;
                    color: #4caf50;
                    margin-bottom: 20px;
                    animation: scaleIn 0.5s ease-out forwards;
                }

                .order-id {
                    font-weight: bold;
                    color: #e50010;
                    background-color: rgba(229, 0, 16, 0.1);
                    display: inline-block;
                    padding: 5px 15px;
                    border-radius: 20px;
                }

                @keyframes fadeInUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                @keyframes scaleIn {
                    from { transform: scale(0); }
                    to { transform: scale(1); }
                }
            `;
            document.head.appendChild(style);
            updateTotal();

            // Reset checkout button
            document.getElementById('checkout-btn').innerHTML = 'Checkout';
            document.getElementById('checkout-btn').disabled = true;

            // Show success notification with more details
            Toastify({
                text: 'Pesanan berhasil dicheckout! Barang telah dihapus dari keranjang.',
                duration: 8000,
                gravity: "top",
                position: "center",
                backgroundColor: "#4caf50",
                stopOnFocus: true,
                onClick: function(){} // Prevents auto-dismiss when clicked
            }).showToast();

            // Update cart count in navbar
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }

            // Optional: Redirect to cart page with success parameter
            // This is useful if the user refreshes the page
            // setTimeout(() => {
            //     window.location.href = `cart.php?checkout_success=1&order_id=${data.order_id}`;
            // }, 3000);
        } else {
            // Reset checkout button
            document.getElementById('checkout-btn').innerHTML = 'Checkout';
            document.getElementById('checkout-btn').disabled = false;

            // Show error notification
            Toastify({
                text: data.message || 'Gagal melakukan checkout. Silakan coba lagi.',
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#f44336",
            }).showToast();
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Reset checkout button
        document.getElementById('checkout-btn').innerHTML = 'Checkout';
        document.getElementById('checkout-btn').disabled = false;

        // Check if error is related to database
        if (error.message && error.message.includes('quantity')) {
            // Show database error message and link to fix
            document.getElementById('database-error').classList.remove('d-none');

            // Show error notification
            Toastify({
                text: 'Terjadi masalah dengan database. Klik link perbaikan di atas.',
                duration: 8000,
                gravity: "top",
                position: "center",
                backgroundColor: "#f44336",
                stopOnFocus: true
            }).showToast();
        } else {
            // Show general error notification
            Toastify({
                text: 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.',
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#f44336",
            }).showToast();
        }
    });
}
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
