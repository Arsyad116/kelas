@extends('layouts.app')

@section('content')

<div class="container">
    <header class="bg-dark text-white">
        @include('layouts.partials.navbar')
    </header>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-4 fw-bold mb-3">Hubungi Kami</h2>
                <p class="lead text-muted">Kami siap membantu Anda. Silakan hubungi kami melalui salah satu cara di bawah ini atau kirim pesan langsung melalui formulir kontak.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h4 class="mb-3">Alamat</h4>
                        <p class="text-muted mb-0">Jl. Puma No. 123, Jakarta Selatan, 12345</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-phone fa-2x"></i>
                        </div>
                        <h4 class="mb-3">Telepon</h4>
                        <p class="text-muted mb-0">+62 21 1234 5678</p>
                        <p class="text-muted mb-0">+62 812 3456 7890</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <h4 class="mb-3">Email</h4>
                        <p class="text-muted mb-0">info@pumastore.com</p>
                        <p class="text-muted mb-0">support@pumastore.com</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Kirim Pesan</h3>
                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
                                        <label for="nama">Nama Lengkap</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="subjek" name="subjek" placeholder="Subjek">
                                <label for="subjek">Subjek</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="pesan" name="pesan" placeholder="Pesan" style="height: 150px" required></textarea>
                                <label for="pesan">Pesan</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Pesan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Lokasi Kami</h3>
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2904357243077!2d106.82687431476908!3d-6.227283395493522!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3f03a5a35c3%3A0xf9ef5e6f48c9df!2sJakarta%20Selatan%2C%20Kota%20Jakarta%20Selatan%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1617345678901!5m2!1sid!2sid" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                        <h4 class="mb-3">Jam Operasional</h4>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Senin - Jumat</span>
                                <span class="badge bg-primary rounded-pill">09:00 - 21:00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Sabtu</span>
                                <span class="badge bg-primary rounded-pill">10:00 - 22:00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Minggu</span>
                                <span class="badge bg-primary rounded-pill">10:00 - 20:00</span>
                            </li>
                        </ul>
                        <h4 class="mb-3">Ikuti Kami</h4>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-outline-primary btn-lg rounded-circle">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-lg rounded-circle">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-lg rounded-circle">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-lg rounded-circle">
                                <i class="fab fa-youtube"></i>
                            </a>
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

    // Contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // You would normally send this data to the server
        // For now, we'll just show an alert
        alert('Terima kasih! Pesan Anda telah dikirim. Kami akan menghubungi Anda segera.');
        this.reset();
    });
</script>
@endpush

@section('styles')
<style>
    .nav-link.active {
        font-weight: bold;
        text-decoration: underline;
    }

    /* Modern Contact Page Styles */
    .bg-primary {
        background-color: #e50010 !important; /* PUMA red */
    }

    .btn-primary {
        background-color: #e50010;
        border-color: #e50010;
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: #c5000e;
        border-color: #c5000e;
    }

    .btn-outline-primary {
        color: #e50010;
        border-color: #e50010;
    }

    .btn-outline-primary:hover, .btn-outline-primary:focus {
        background-color: #e50010;
        border-color: #e50010;
        color: white;
    }

    .badge.bg-primary {
        background-color: #e50010 !important;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .form-control:focus {
        border-color: #e50010;
        box-shadow: 0 0 0 0.25rem rgba(229, 0, 16, 0.25);
    }

    .form-floating > .form-control:focus ~ label {
        color: #e50010;
    }

    .rounded-circle {
        transition: all 0.3s ease;
    }

    .rounded-circle:hover {
        transform: translateY(-3px);
    }

    .list-group-item {
        transition: background-color 0.3s ease;
    }

    .list-group-item:hover {
        background-color: rgba(229, 0, 16, 0.05);
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card, .form-control, .btn {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .card:nth-child(3) {
        animation-delay: 0.3s;
    }
</style>
@endsection