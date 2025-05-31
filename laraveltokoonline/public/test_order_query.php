<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Order Query Test</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Test the query used in OrderController
    $whereClause = "";
    $limit = 10;
    $offset = 0;
    
    // Count total orders for pagination
    $totalQuery = "SELECT COUNT(*) as total FROM pesanan p " . $whereClause;
    $stmt = $conn->prepare($totalQuery);
    $stmt->execute();
    $totalRow = $stmt->get_result()->fetch_assoc();
    $totalOrders = $totalRow['total'];
    
    echo "<p>Total orders: {$totalOrders}</p>";
    
    // Get orders for current page, joining with pesanan_item and produk to get product images
    $query = "SELECT p.id, p.tanggal, p.total_harga, p.status, p.nama_barang,
                  GROUP_CONCAT(DISTINCT pi.produk_id) as product_ids,
                  GROUP_CONCAT(DISTINCT pr.gambar) as product_images
              FROM pesanan p
              LEFT JOIN pesanan_item pi ON p.id = pi.pesanan_id
              LEFT JOIN produk pr ON pi.produk_id = pr.id
              " . $whereClause . "
              GROUP BY p.id, p.tanggal, p.total_harga, p.status, p.nama_barang
              ORDER BY p.tanggal DESC LIMIT $limit OFFSET $offset";
    
    echo "<p>Query: " . htmlspecialchars($query) . "</p>";
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<h3>Query Results:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Tanggal</th><th>Total Harga</th><th>Status</th><th>Nama Barang</th><th>Product IDs</th><th>Product Images</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['total_harga'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['status'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_barang'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['product_ids'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['product_images'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No results found for the query.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Query Error: " . $e->getMessage() . "</p>";
        
        // Try a simpler query to check if the tables exist
        echo "<h3>Checking tables:</h3>";
        
        $tables = ['pesanan', 'pesanan_item', 'produk'];
        foreach ($tables as $table) {
            $checkQuery = "SELECT COUNT(*) as count FROM $table";
            try {
                $result = $conn->query($checkQuery);
                if ($result) {
                    $count = $result->fetch_assoc()['count'];
                    echo "<p>Table '$table' exists and has $count records.</p>";
                } else {
                    echo "<p style='color:red'>Error checking table '$table': " . $conn->error . "</p>";
                }
            } catch (Exception $tableEx) {
                echo "<p style='color:red'>Error with table '$table': " . $tableEx->getMessage() . "</p>";
            }
        }
        
        // Check if GROUP BY is causing issues
        echo "<h3>Testing query without GROUP BY:</h3>";
        $simpleQuery = "SELECT p.id, p.tanggal, p.total_harga, p.status, p.nama_barang
                      FROM pesanan p
                      ORDER BY p.tanggal DESC LIMIT $limit";
        
        try {
            $result = $conn->query($simpleQuery);
            if ($result && $result->num_rows > 0) {
                echo "<p>Simple query works. Results:</p>";
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Tanggal</th><th>Total Harga</th><th>Status</th><th>Nama Barang</th></tr>";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['total_harga'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['status'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_barang'] ?? 'NULL') . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>No results found for simple query or query failed.</p>";
            }
        } catch (Exception $simpleEx) {
            echo "<p style='color:red'>Simple query error: " . $simpleEx->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn)) {
        $conn->close();
        echo "<p>Database connection closed.</p>";
    }
}
?>
