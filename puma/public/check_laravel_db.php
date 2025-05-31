<?php
// Memuat autoloader Laravel
require __DIR__.'/../vendor/autoload.php';

// Memuat aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Menggunakan facade DB
use Illuminate\Support\Facades\DB;

// Menampilkan informasi koneksi database
echo "<h2>Laravel Database Connection Check</h2>";

try {
    // Mendapatkan konfigurasi database
    $connection = config('database.default');
    $config = config('database.connections.' . $connection);
    
    echo "<p>Default Connection: <strong>{$connection}</strong></p>";
    echo "<p>Database Name: <strong>{$config['database']}</strong></p>";
    echo "<p>Database Host: <strong>{$config['host']}</strong></p>";
    
    // Mencoba koneksi
    $pdo = DB::connection()->getPdo();
    echo "<p style='color:green'>Database connection successful!</p>";
    
    // Memeriksa tabel pesanan
    $orders = DB::table('pesanan')->orderBy('id', 'desc')->limit(10)->get();
    
    if (count($orders) > 0) {
        echo "<h3>Recent Orders in Laravel Database:</h3>";
        echo "<table border='1'>";
        echo "<tr>";
        foreach ((array)$orders[0] as $key => $value) {
            echo "<th>{$key}</th>";
        }
        echo "</tr>";
        
        foreach ($orders as $order) {
            echo "<tr>";
            foreach ((array)$order as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found in the database.</p>";
    }
    
    // Memeriksa tabel pesanan_item
    $items = DB::table('pesanan_item')->orderBy('id', 'desc')->limit(10)->get();
    
    if (count($items) > 0) {
        echo "<h3>Recent Order Items in Laravel Database:</h3>";
        echo "<table border='1'>";
        echo "<tr>";
        foreach ((array)$items[0] as $key => $value) {
            echo "<th>{$key}</th>";
        }
        echo "</tr>";
        
        foreach ($items as $item) {
            echo "<tr>";
            foreach ((array)$item as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No order items found in the database.</p>";
    }
    
} catch (\Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
