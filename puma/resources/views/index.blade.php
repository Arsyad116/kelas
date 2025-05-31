@extends('layouts.app')

@section('content')

<div class="container">
    <header class="bg-dark text-white">
        @include('layouts.partials.navbar')
    </header>

    <div id="bannerCarousel" class="carousel slide carousel-fade mx-auto" data-ride="carousel">
        <div class="carousel-inner">
            @if (!empty($banners))
                @foreach ($banners as $index => $banner)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        @if (filter_var($banner, FILTER_VALIDATE_URL))
                            <img src="{{ $banner }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                        @else
                            <img src="{{ asset('images/' . $banner) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                        @endif
                    </div>
                @endforeach
            @else
                <div class="carousel-item active">
                    <img src="{{ asset('images/bn1.png') }}" class="d-block w-100" alt="Default Banner">
                </div>
            @endif
        </div>
    </div>

    <section class="container my-4">
        <div class="row" id="productContainer">
            @if(isset($products) && count($products) > 0)
                @foreach($products as $product)
                <div class="col-md-4 product-item">
                    <div class="card">
                        <img src="{{ asset('images/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <a href="{{ url('/produk') }}"><button class="btn btn-secondary w-100 add-to-cart">Add to Cart</button></a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-md-4 product-item">
                    <div class="card">
                        <img src="{{ asset('images/black.jpeg') }}" class="card-img-top" alt="PUMA Palermo I">
                        <div class="card-body">
                            <h5 class="card-title">PUMA Palermo I</h5>
                            <p class="card-text">Harga: Rp 876.000</p>
                            <a href="{{ url('/produk') }}"><button class="btn btn-secondary w-100 add-to-cart">Add to Cart</button></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 product-item">
                    <div class="card">
                        <img src="{{ asset('images/brown.jpeg') }}" class="card-img-top" alt="PUMA Palermo II">
                        <div class="card-body">
                            <h5 class="card-title">PUMA Palermo II</h5>
                            <p class="card-text">Harga: Rp 680.000</p>
                            <a href="{{ url('/produk') }}"><button class="btn btn-secondary w-100 add-to-cart">Add to Cart</button></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 product-item">
                    <div class="card">
                        <img src="{{ asset('images/green.jpeg') }}" class="card-img-top" alt="PUMA Palermo III">
                        <div class="card-body">
                            <h5 class="card-title">PUMA Palermo III</h5>
                            <p class="card-text">Harga: Rp 750.000</p>
                            <a href="{{ url('/produk') }}"><button class="btn btn-secondary w-100 add-to-cart">Add to Cart</button></a>
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

    <!-- Scripts -->
    @push('scripts')
    <script>
    // Function to update cart count
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
            // Hide badge if cart is empty
            cartCountElement.style.display = cartCount > 0 ? 'block' : 'none';
        }
    }

    // Update cart count when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        checkLogin();

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const productItems = document.getElementsByClassName('product-item');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                Array.from(productItems).forEach(item => {
                    const title = item.querySelector('.card-title').textContent.toLowerCase();
                    if (title.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });

    // Update cart count whenever cart is modified
    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            updateCartCount();
        }
    });

    // Call this function whenever you add/remove items from cart
    function addToCart(item) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.push(item);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }

    function removeFromCart(index) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }

    function checkLogin() {
        let loggedIn = localStorage.getItem('loggedIn');
        if (loggedIn === 'true') {
            document.getElementById('auth-buttons').innerHTML = '<button class="btn btn-danger" onclick="logout()">Logout</button>';
        }
    }

    function login() {
        localStorage.setItem('loggedIn', 'true');
        location.reload();
    }

    function logout() {
        localStorage.removeItem('loggedIn');
        location.reload();
    }
    </script>
    @endpush
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="close text-light" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" method="POST" action="{{ url('/login') }}">
                        @csrf
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control bg-secondary text-light" required>
                        </div>
                        <button type="button" class="btn btn-primary btn-block" id="loginButton">Login</button>
                    </form>
                    <div id="loginNotification" class="mt-3 notification"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Register</h5>
                    <button type="button" class="close text-light" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" method="POST" action="{{ url('/register') }}">
                        @csrf
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control bg-secondary text-light" required>
                        </div>
                        <button type="button" class="btn btn-primary btn-block" id="registerButton">Register</button>
                    </form>
                    <div id="registerNotification" class="mt-3 notification"></div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Authentication scripts
    document.addEventListener('DOMContentLoaded', function() {
        // Login button handler
        $('#loginButton').on('click', function() {
            // Show loading state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            $(this).prop('disabled', true);

            // Get form data
            var email = $('#loginForm input[name="email"]').val();
            var password = $('#loginForm input[name="password"]').val();
            var token = $('#loginForm input[name="_token"]').val();

            // Clear previous notifications
            $('#loginNotification').removeClass('success error').hide();

            // Send AJAX request
            $.ajax({
                url: '{{ url("/login") }}',
                type: 'POST',
                data: {
                    email: email,
                    password: password,
                    _token: token
                },
                success: function(data) {
                    if (data.success) {
                        // Show success message
                        $('#loginNotification').html('Login successful! Redirecting...').addClass('success').show();

                        // Set logged in state and reload
                        localStorage.setItem('loggedIn', 'true');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        // Show error message
                        $('#loginNotification').html(data.error || 'Login failed. Please check your credentials.').addClass('error').show();

                        // Reset button
                        $('#loginButton').html('Login').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    // Log error details
                    console.error('Login Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);

                    // Show error message
                    $('#loginNotification').html('An error occurred. Please try again.').addClass('error').show();

                    // Reset button
                    $('#loginButton').html('Login').prop('disabled', false);
                }
            });
        });

        // Register button handler
        $('#registerButton').on('click', function() {
            // Show loading state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            $(this).prop('disabled', true);

            // Get form data
            var username = $('#registerForm input[name="username"]').val();
            var email = $('#registerForm input[name="email"]').val();
            var password = $('#registerForm input[name="password"]').val();
            var password_confirmation = $('#registerForm input[name="password_confirmation"]').val();
            var token = $('#registerForm input[name="_token"]').val();

            // Validate passwords match
            if (password !== password_confirmation) {
                $('#registerNotification').html('Passwords do not match').addClass('error').show();
                $(this).html('Register').prop('disabled', false);
                return;
            }

            // Clear previous notifications
            $('#registerNotification').removeClass('success error').hide();

            // Send AJAX request
            $.ajax({
                url: '{{ url("/register") }}',
                type: 'POST',
                data: {
                    username: username,
                    email: email,
                    password: password,
                    password_confirmation: password_confirmation,
                    _token: token
                },
                success: function(data) {
                    if (data.success) {
                        // Show success message
                        $('#registerNotification').html('Registration successful! Please login.').addClass('success').show();

                        // Switch to login modal after a delay
                        setTimeout(function() {
                            $('#registerModal').modal('hide');
                            setTimeout(function() {
                                $('#loginModal').modal('show');
                            }, 500);
                        }, 1500);
                    } else {
                        // Show error message
                        $('#registerNotification').html(data.error || 'Registration failed. Please try again.').addClass('error').show();

                        // Reset button
                        $('#registerButton').html('Register').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    // Log error details
                    console.error('Register Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);

                    // Show error message
                    $('#registerNotification').html('An error occurred. Please try again.').addClass('error').show();

                    // Reset button
                    $('#registerButton').html('Register').prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush

</div> <!-- End of container -->
@endsection
