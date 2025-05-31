<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variables
$message = "";
$messageType = "";

// Handle customer deletion
if (isset($_GET['hapus'])) {
    $customerId = intval($_GET['hapus']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);

    if ($stmt->execute()) {
        $message = "Customer berhasil dihapus!";
        $messageType = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $messageType = "error";
    }
    
    $stmt->close();
}

// Get customers with pagination and search
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build the WHERE clause for search
$whereClause = '';
if (!empty($search)) {
    $whereClause = " WHERE 
        id LIKE '%$search%' OR 
        username LIKE '%$search%' OR 
        email LIKE '%$search%'";
}

// Count total customers for pagination
$totalQuery = "SELECT COUNT(*) as total FROM users" . $whereClause;
$totalResult = $conn->query($totalQuery);

// Initialize $totalCustomers to avoid undefined variable error
$totalCustomers = 0;
if ($totalResult && $totalResult->num_rows > 0) {
    $totalRow = $totalResult->fetch_assoc();
    $totalCustomers = $totalRow['total'];
}

$totalPages = ceil($totalCustomers / $limit);

// Get customers for current page
$query = "SELECT * FROM users" . $whereClause . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Initialize $result if query failed
if (!$result) {
    $result = new stdClass();
    $result->num_rows = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - PUMA Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        
        .app-container {
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(to bottom, #1a1a2e, #16213e);
            color: #fff;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .nav-link.active {
            background-color: #e50010; /* PUMA red */
            color: white;
            font-weight: 500;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }
        
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 30px;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        /* Search Container Styles */
        .search-container {
            margin-bottom: 20px;
        }
        
        .search-input {
            height: 46px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0 15px;
            width: 300px;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            border-color: #e50010;
            box-shadow: 0 0 0 0.2rem rgba(229, 0, 16, 0.25);
            outline: none;
        }
        
        /* Button Styles */
        .btn {
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 500;
            padding: 0 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #e50010; /* PUMA red */
            border-color: #e50010;
        }
        
        .btn-primary:hover {
            background-color: #c2000e;
            border-color: #c2000e;
        }
        
        .btn-outline-secondary {
            color: #555;
            border-color: #d1d1d1;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #333;
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: none;
            border-bottom: 1px solid #e0e0e0;
            color: #555;
        }
        
        .table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Action Button Styles */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 5px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            color: #555;
        }
        
        .btn-delete {
            color: #e50010;
            border-color: rgba(229, 0, 16, 0.3);
        }
        
        .btn-delete:hover {
            background-color: rgba(229, 0, 16, 0.1);
            border-color: rgba(229, 0, 16, 0.5);
            color: #e50010;
        }
        
        /* Pagination Styles */
        .pagination {
            margin-top: 25px;
            justify-content: center;
        }
        
        .page-item {
            margin: 0 3px;
        }
        
        .page-link {
            border: none;
            border-radius: 8px;
            color: #555;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .page-item.active .page-link {
            background-color: #e50010; /* PUMA red */
            color: white;
            box-shadow: 0 2px 5px rgba(229, 0, 16, 0.3);
        }
        
        .page-link:hover {
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* Empty State Styles */
        .empty-state {
            padding: 40px;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 15px;
        }
        
        .empty-state-text {
            color: #888;
            font-size: 1.1rem;
        }
        
        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
                padding: 20px;
            }
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .search-input {
                width: 100%;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .d-flex {
                flex-direction: column;
            }
            
            .btn {
                margin-top: 10px;
                width: 100%;
            }
            
            .search-input + button {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-area">
                <img src="../images/logo.png" alt="PUMA" class="logo-img">
                <h5 class="mt-3 text-white fw-bold">Admin Dashboard</h5>
            </div>
            
            <nav class="mt-4">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                <a href="orders.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="customers.php" class="nav-link active">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                <a href="banners.php" class="nav-link">
                    <i class="fas fa-image"></i>
                    <span>Banners</span>
                </a>
                <a href="../logout.php" class="nav-link mt-5">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1 class="page-title">Customer Management</h1>
                <button class="btn btn-primary d-none d-md-flex">
                    <i class="fas fa-file-export me-2"></i> Export Customers
                </button>
            </div>

            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo $message; ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="search-container">
                        <form action="" method="GET" class="d-flex align-items-center">
                            <div class="position-relative flex-grow-1">
                                <input type="text" name="search" class="form-control search-input" placeholder="Search by ID, username, or email..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="fas fa-search me-2"></i> Search
                            </button>
                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                <a href="customers.php" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="35%">Username</th>
                                    <th width="45%">Email</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td class="text-start"><?php echo $row['username']; ?></td>
                                            <td class="text-start"><?php echo $row['email']; ?></td>
                                            <td>
                                                <a href="customers.php?hapus=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="fas fa-users-slash"></i>
                                                <p class="empty-state-text">No customers found</p>
                                                <?php if (!empty($search)): ?>
                                                    <a href="customers.php" class="btn btn-outline-secondary mt-3">
                                                        <i class="fas fa-times me-2"></i> Clear Search
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                    <div class="mt-4 text-center text-muted small">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalCustomers); ?> of <?php echo $totalCustomers; ?> customers
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add responsive sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            // Create toggle button for mobile
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'navbar-toggler position-fixed top-0 start-0 m-3 p-2 rounded-circle shadow bg-white d-md-none';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.style.zIndex = '1050';
            toggleBtn.style.width = '40px';
            toggleBtn.style.height = '40px';
            document.body.appendChild(toggleBtn);
            
            // Toggle sidebar on click
            toggleBtn.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar.style.width === '200px') {
                    sidebar.style.width = '0';
                    sidebar.style.padding = '0';
                } else {
                    sidebar.style.width = '200px';
                    sidebar.style.padding = '20px';
                }
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                const sidebar = document.querySelector('.sidebar');
                const toggleBtn = document.querySelector('.navbar-toggler');
                if (window.innerWidth < 768 && 
                    !sidebar.contains(event.target) && 
                    !toggleBtn.contains(event.target) &&
                    sidebar.style.width === '200px') {
                    sidebar.style.width = '0';
                    sidebar.style.padding = '0';
                }
            });
        });
    </script>
</body>
</html>