@extends('layouts.app')

@section('content')
<header class="bg-dark text-white">
    @include('layouts.partials.navbar')
</header>

<div class="container">

<section id="tentang-kami" class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Tentang PUMA STORE</h2>
                    <p class="lead text-center mb-5">PUMA STORE adalah destinasi utama untuk produk olahraga dan lifestyle berkualitas premium. Kami berkomitmen untuk memberikan pengalaman belanja online yang aman, nyaman, dan terpercaya bagi pelanggan kami.</p>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-dark text-white">
                                <div class="card-body">
                                    <h3 class="card-title"><i class="fas fa-eye mb-3"></i> Visi</h3>
                                    <p class="card-text">Menjadi toko online terpercaya dan terdepan di Indonesia yang menyediakan produk olahraga dan lifestyle berkualitas premium dengan harga terbaik.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-dark text-white">
                                <div class="card-body">
                                    <h3 class="card-title"><i class="fas fa-bullseye mb-3"></i> Misi</h3>
                                    <ul class="card-text">
                                        <li>Menyediakan produk berkualitas premium dengan harga terbaik</li>
                                        <li>Memberikan pelayanan terbaik kepada pelanggan</li>
                                        <li>Mengutamakan kepuasan pelanggan</li>
                                        <li>Menjaga kepercayaan pelanggan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-dark text-white">
                                <div class="card-body">
                                    <h3 class="card-title"><i class="fas fa-history mb-3"></i> Sejarah Kami</h3>
                                    <p class="card-text">Didirikan pada tahun 2020, PUMA STORE telah berkembang menjadi salah satu toko online terpercaya di Indonesia. Kami telah melayani ribuan pelanggan dan terus berkomitmen untuk memberikan yang terbaik.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-dark text-white">
                                <div class="card-body">
                                    <h3 class="card-title"><i class="fas fa-users mb-3"></i> Tim Kami</h3>
                                    <p class="card-text">Tim kami terdiri dari profesional berpengalaman yang berdedikasi untuk memberikan layanan terbaik. Kami selalu siap membantu Anda dengan kebutuhan belanja online Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-dark text-white">
                                <div class="card-body">
                                    <h3 class="card-title"><i class="fas fa-map-marker-alt mb-3"></i> Alamat</h3>
                                    <p class="card-text">Jl. Puma No. 123, Jakarta Selatan, 12345</p>
                                    <p class="card-text">Jam Operasional:<br>Senin - Jumat: 09:00 - 21:00<br>Sabtu - Minggu: 10:00 - 22:00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 bg-dark text-white">
                                <div class="card-body">
                                    <h3 class="card-title"><i class="fas fa-envelope mb-3"></i> Kontak</h3>
                                    <p class="card-text">Email: info@pumastore.com</p>
                                    <p class="card-text">Telepon: +62 21 1234 5678</p>
                                    <p class="card-text">WhatsApp: +62 812 3456 7890</p>
                                    <div class="mt-3">
                                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2025 PUMA STORE. Hak Cipta Dilindungi.</p>
</footer>

</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-light" id="loginModalLabel">Login</h5>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-light" id="registerModalLabel">Register</h5>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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

@endsection

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

@section('styles')
<style>
    .nav-link.active {
        font-weight: bold;
        text-decoration: underline;
    }

    #tentang-kami {
        background: rgba(0, 0, 0, 0.6);
        padding: 50px;
        border-radius: 10px;
        color: #ffffff;
        text-align: center;
    }

    #tentang-kami h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    #tentang-kami p {
        font-size: 1.2rem;
    }

    .misi-visi {
        display: flex;
        justify-content: space-around;
        text-align: left;
    }

    .misi-visi div {
        background: rgba(255, 255, 255, 0.1);
        padding: 20px;
        border-radius: 10px;
    }

    .col-md-6 {
        margin-bottom: 20px;
    }
</style>
@endsection
