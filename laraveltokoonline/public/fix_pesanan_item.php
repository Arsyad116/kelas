<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Fix Pesanan Item Table</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check if pesanan_item table exists
    $result = $conn->query("SHOW TABLES LIKE 'pesanan_item'");
    if ($result->num_rows > 0) {
        echo "<p>Table 'pesanan_item' exists.</p>";
        
        // Check structure
        $result = $conn->query("DESCRIBE pesanan_item");
        echo "<h3>Current structure of 'pesanan_item' table:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Check data
        $result = $conn->query("SELECT * FROM pesanan_item LIMIT 10");
        if ($result->num_rows > 0) {
            echo "<p>Table has data (" . $result->num_rows . " rows).</p>";
        } else {
            echo "<p style='color:red'>Table 'pesanan_item' is empty.</p>";
            
            // Check pesanan table
            $result = $conn->query("SELECT * FROM pesanan LIMIT 10");
            if ($result->num_rows > 0) {
                echo "<p>Table 'pesanan' has data (" . $result->num_rows . " rows).</p>";
                
                // Check detail_pesanan table
                $result = $conn->query("SHOW TABLES LIKE 'detail_pesanan'");
                if ($result->num_rows > 0) {
                    echo "<p>Table 'detail_pesanan' exists.</p>";
                    
                    $result = $conn->query("SELECT * FROM detail_pesanan LIMIT 10");
                    if ($result->num_rows > 0) {
                        echo "<p>Table 'detail_pesanan' has data (" . $result->num_rows . " rows).</p>";
                        
                        // Copy data from detail_pesanan to pesanan_item
                        echo "<h3>Copying data from detail_pesanan to pesanan_item...</h3>";
                        
                        // First, get all orders with details
                        $result = $conn->query("SELECT d.id_pesanan, d.nama_produk, d.harga, d.quantity FROM detail_pesanan d");
                        
                        $insertCount = 0;
                        $errorCount = 0;
                        
                        while ($row = $result->fetch_assoc()) {
                            // Try to find product ID by name
                            $product_name = $row['nama_produk'];
                            $find_product = $conn->prepare("SELECT id FROM produk WHERE nama LIKE ? LIMIT 1");
                            $search_name = "%$product_name%";
                            $find_product->bind_param("s", $search_name);
                            $find_product->execute();
                            $find_product->store_result();
                            
                            if ($find_product->num_rows > 0) {
                                $find_product->bind_result($produk_id);
                                $find_product->fetch();
                                
                                // Insert into pesanan_item with real product ID
                                $stmt_item = $conn->prepare("INSERT INTO pesanan_item (pesanan_id, produk_id, quantity, harga) VALUES (?, ?, ?, ?)");
                                $stmt_item->bind_param("iiid",
                                    $row['id_pesanan'],
                                    $produk_id,
                                    $row['quantity'],
                                    $row['harga']
                                );
                                
                                if ($stmt_item->execute()) {
                                    $insertCount++;
                                } else {
                                    $errorCount++;
                                    echo "<p style='color:red'>Error inserting item: " . $stmt_item->error . "</p>";
                                }
                                
                                $stmt_item->close();
                            } else {
                                // If product not found, create a temporary one
                                $insert_product = $conn->prepare("INSERT INTO produk (nama, harga) VALUES (?, ?)");
                                $insert_product->bind_param("sd", $row['nama_produk'], $row['harga']);
                                
                                if ($insert_product->execute()) {
                                    $produk_id = $insert_product->insert_id;
                                    
                                    // Insert into pesanan_item with new product ID
                                    $stmt_item = $conn->prepare("INSERT INTO pesanan_item (pesanan_id, produk_id, quantity, harga) VALUES (?, ?, ?, ?)");
                                    $stmt_item->bind_param("iiid",
                                        $row['id_pesanan'],
                                        $produk_id,
                                        $row['quantity'],
                                        $row['harga']
                                    );
                                    
                                    if ($stmt_item->execute()) {
                                        $insertCount++;
                                    } else {
                                        $errorCount++;
                                        echo "<p style='color:red'>Error inserting item: " . $stmt_item->error . "</p>";
                                    }
                                    
                                    $stmt_item->close();
                                } else {
                                    $errorCount++;
                                    echo "<p style='color:red'>Error creating product: " . $insert_product->error . "</p>";
                                }
                                
                                $insert_product->close();
                            }
                            
                            $find_product->close();
                        }
                        
                        echo "<p>Inserted $insertCount items with $errorCount errors.</p>";
                        
                        // Check if data was copied
                        $result = $conn->query("SELECT * FROM pesanan_item LIMIT 10");
                        if ($result->num_rows > 0) {
                            echo "<p style='color:green'>Successfully copied data to pesanan_item table.</p>";
                            
                            // Show the data
                            echo "<h3>Data in pesanan_item table:</h3>";
                            echo "<table border='1'>";
                            echo "<tr><th>ID</th><th>Pesanan ID</th><th>Produk ID</th><th>Quantity</th><th>Harga</th></tr>";
                            
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id'] ?? 'NULL') . "</td>";
                                echo "<td>" . htmlspecialchars($row['pesanan_id'] ?? 'NULL') . "</td>";
                                echo "<td>" . htmlspecialchars($row['produk_id'] ?? 'NULL') . "</td>";
                                echo "<td>" . htmlspecialchars($row['quantity'] ?? 'NULL') . "</td>";
                                echo "<td>" . htmlspecialchars($row['harga'] ?? 'NULL') . "</td>";
                                echo "</tr>";
                            }
                            
                            echo "</table>";
                        } else {
                            echo "<p style='color:red'>Failed to copy data to pesanan_item table.</p>";
                        }
                    } else {
                        echo "<p style='color:red'>Table 'detail_pesanan' is empty.</p>";
                    }
                } else {
                    echo "<p style='color:red'>Table 'detail_pesanan' does not exist.</p>";
                }
            } else {
                echo "<p style='color:red'>Table 'pesanan' is empty.</p>";
            }
        }
    } else {
        echo "<p style='color:red'>Table 'pesanan_item' does not exist.</p>";
        
        // Create the table
        echo "<h3>Creating pesanan_item table...</h3>";
        
        $sql = "CREATE TABLE IF NOT EXISTS pesanan_item (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            pesanan_id INT(11) NOT NULL,
            produk_id INT(11) NOT NULL,
            quantity INT(11) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
        )";
        
        if ($conn->query($sql)) {
            echo "<p style='color:green'>Table 'pesanan_item' created successfully.</p>";
        } else {
            echo "<p style='color:red'>Error creating table: " . $conn->error . "</p>";
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
