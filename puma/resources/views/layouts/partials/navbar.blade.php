<nav class="container navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-navbar">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('produk') ? 'active' : '' }}" href="{{ url('/produk') }}">Produk</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('tentang') ? 'active' : '' }}" href="{{ url('/tentang') }}">Tentang Kami</a></li>
            <li class="nav-item"><a class="nav-link {{ Request::is('kontak') ? 'active' : '' }}" href="{{ url('/kontak') }}">Kontak</a></li>
        </ul>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Cari produk...">
            <button><img src="{{ asset('images/search.png') }}" alt="Search" width="20"></button>
        </div>
        <div class="auth-buttons" id="auth-buttons">
            <button class="btn btn-outline-light" data-toggle="modal" data-target="#registerModal">Register</button>
            <button class="btn btn-light" data-toggle="modal" data-target="#loginModal">Login</button>
        </div>
        <a href="{{ url('/cart') }}" class="position-relative d-inline-block text-decoration-none">
            <button class="btn btn-light" style="margin-left: 25px">
                <i class="fas fa-shopping-cart"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                    0
                </span>
            </button>
        </a>
    </div>
</nav>
