@extends('layouts.app')

@section('content')
<div class="container">
    <header class="bg-dark text-white">
        <nav class="container navbar navbar-expand-lg navbar-dark">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-navbar">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item active"><a class="nav-link" href="{{ url('/produk') }}">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/tentang') }}">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/kontak') }}">Kontak</a></li>
                </ul>
                <div class="search-container">
                    <input type="text" id="search-input" placeholder="Cari produk...">
                    <button><img src="{{ asset('images/search.png') }}" alt="Search" width="20"></button>
                </div>
            </div>
            <a href="{{ url('/cart') }}" class="position-relative d-inline-block text-decoration-none">
                <button class="btn btn-light" style="margin-left: 25px">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                         0
                    </span>
                </button>
            </a>
        </nav>
    </header>

<section class="container my-4">
    <div class="row" id="productContainer">
        @if(isset($products) && count($products) > 0)
            @foreach($products as $product)
            <div class="col-md-4 product-item">
                <div class="card" data-id="{{ $product->id }}">
                    <img src="{{ asset('images/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <button class="btn btn-secondary w-100 add-to-cart"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ asset('images/' . $product->image) }}">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-md-4 product-item">
                <div class="card" data-id="1">
                    <img src="{{ asset('images/black.jpeg') }}" class="card-img-top" alt="PUMA Palermo I">
                    <div class="card-body">
                        <h5 class="card-title">PUMA Palermo I</h5>
                        <p class="card-text">Harga: Rp 876.000</p>
                        <button class="btn btn-secondary w-100 add-to-cart" data-name="PUMA Palermo I" data-price="876000" data-image="{{ asset('images/black.jpeg') }}">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 product-item">
                <div class="card" data-id="2">
                    <img src="{{ asset('images/brown.jpeg') }}" class="card-img-top" alt="PUMA Palermo II">
                    <div class="card-body">
                        <h5 class="card-title">PUMA Palermo II</h5>
                        <p class="card-text">Harga: Rp 680.000</p>
                        <button class="btn btn-secondary w-100 add-to-cart" data-name="PUMA Palermo II" data-price="680000" data-image="{{ asset('images/brown.jpeg') }}">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 product-item">
                <div class="card" data-id="3">
                    <img src="{{ asset('images/green.jpeg') }}" class="card-img-top" alt="PUMA Palermo III">
                    <div class="card-body">
                        <h5 class="card-title">PUMA Palermo III</h5>
                        <p class="card-text">Harga: Rp 750.000</p>
                        <button class="btn btn-secondary w-100 add-to-cart" data-name="PUMA Palermo III" data-price="750000" data-image="{{ asset('images/green.jpeg') }}">Add to Cart</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<footer class="bg-dark text-white text-center py-3">
    <nav>
        <ul class="list-inline">
            <li class="list-inline-item"><a class="text-white" href="{{ url('/') }}">Menu</a></li>
            <li class="list-inline-item"><a class="text-white" href="{{ url('/pembayaran') }}">Pembayaran</a></li>
            <li class="list-inline-item"><a class="text-white" href="{{ url('/medsos') }}">Medsos</a></li>
            <li class="list-inline-item"><a class="text-white" href="{{ url('/kontak') }}">Kontak</a></li>
        </ul>
    </nav>
</footer>

@push('scripts')
<script>
    // Function to update cart count
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
            cartCountElement.style.display = cartCount > 0 ? 'block' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();

        // Search functionality
        document.getElementById("search-input").addEventListener("keyup", function() {
            let searchText = this.value.toLowerCase();
            let products = document.querySelectorAll(".product-item");

            products.forEach(product => {
                let productName = product.querySelector(".card-title").innerText.toLowerCase();
                if (productName.includes(searchText)) {
                    product.style.display = "block";
                } else {
                    product.style.display = "none";
                }
            });
        });

        // Add to Cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productName = this.getAttribute('data-name');
                const productPrice = this.getAttribute('data-price');
                const productImage = this.getAttribute('data-image');

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart.push({
                    name: productName,
                    price: productPrice,
                    image: productImage
                });
                localStorage.setItem('cart', JSON.stringify(cart));

                this.textContent = 'Added!';
                this.disabled = true;
                setTimeout(() => {
                    this.textContent = 'Add to Cart';
                    this.disabled = false;
                }, 1500);

                updateCartCount();
                window.location.href = '{{ url('/cart') }}';
            });
        });
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            updateCartCount();
        }
    });
</script>
@endpush

</div> <!-- End of container -->
@endsection