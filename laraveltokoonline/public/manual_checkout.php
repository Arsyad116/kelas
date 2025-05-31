<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Manual Checkout Test</h2>";

// Create sample order data
$orderData = [
    'items' => [
        [
            'nama_produk' => 'Sepatu Puma Running',
            'harga' => 1500000,
            'quantity' => 1,
            'image' => 'images/products/puma1.jpg'
        ],
        [
            'nama_produk' => 'Kaos Puma Sport',
            'harga' => 350000,
            'quantity' => 2,
            'image' => 'images/products/puma2.jpg'
        ]
    ],
    'total' => 2200000 // 1500000 + (350000 * 2)
];

echo "<p>Sample order data:</p>";
echo "<pre>" . htmlspecialchars(json_encode($orderData, JSON_PRETTY_PRINT)) . "</pre>";

// Send the data to proses_checkout.php
$ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . '/proses_checkout.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

echo "<p>Sending data to proses_checkout.php...</p>";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "<p>HTTP Response Code: $httpCode</p>";

if ($error) {
    echo "<p style='color:red'>cURL Error: $error</p>";
} else {
    echo "<p>Response:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Parse the response
    $responseData = json_decode($response, true);
    
    if (isset($responseData['success']) && $responseData['success']) {
        echo "<p style='color:green'>Checkout successful! Order ID: " . $responseData['order_id'] . "</p>";
        
        // Check the database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dbpuma";
        
        try {
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Get the order from database
            $order_id = $responseData['order_id'];
            $result = $conn->query("SELECT * FROM pesanan WHERE id = $order_id");
            
            if ($result->num_rows > 0) {
                $order = $result->fetch_assoc();
                
                echo "<h3>Order in Database:</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Field</th><th>Value</th></tr>";
                
                foreach ($order as $field => $value) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($field) . "</td>";
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                // Get order details
                $result = $conn->query("SELECT * FROM detail_pesanan WHERE id_pesanan = $order_id");
                
                if ($result->num_rows > 0) {
                    echo "<h3>Order Details in Database:</h3>";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Nama Produk</th><th>Harga</th><th>Quantity</th></tr>";
                    
                    while ($detail = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($detail['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($detail['nama_produk']) . "</td>";
                        echo "<td>Rp " . number_format($detail['harga'], 0, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($detail['quantity']) . "</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                } else {
                    echo "<p>No order details found.</p>";
                }
                
                // Get pesanan_item records
                $result = $conn->query("SELECT pi.*, p.nama FROM pesanan_item pi JOIN produk p ON pi.produk_id = p.id WHERE pi.pesanan_id = $order_id");
                
                if ($result->num_rows > 0) {
                    echo "<h3>Pesanan Item Records in Database:</h3>";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Produk ID</th><th>Nama Produk</th><th>Harga</th><th>Quantity</th></tr>";
                    
                    while ($item = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($item['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($item['produk_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($item['nama']) . "</td>";
                        echo "<td>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                } else {
                    echo "<p>No pesanan_item records found.</p>";
                }
            } else {
                echo "<p style='color:red'>Order not found in database!</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color:red'>Database Error: " . $e->getMessage() . "</p>";
        } finally {
            if (isset($conn)) $conn->close();
        }
    } else {
        echo "<p style='color:red'>Checkout failed: " . ($responseData['message'] ?? 'Unknown error') . "</p>";
    }
}
?>
