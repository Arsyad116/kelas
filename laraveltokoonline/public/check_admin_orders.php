<?php
// Memuat autoloader Laravel
require __DIR__.'/../vendor/autoload.php';

// Memuat aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Menggunakan facade DB
use Illuminate\Support\Facades\DB;

// Menampilkan informasi koneksi database
echo "<h2>Admin Orders Check</h2>";

try {
    // Mendapatkan konfigurasi database
    $connection = config('database.default');
    $config = config('database.connections.' . $connection);
    
    echo "<p>Default Connection: <strong>{$connection}</strong></p>";
    echo "<p>Database Name: <strong>{$config['database']}</strong></p>";
    
    // Memeriksa SQL mode saat ini
    $sqlMode = DB::select("SELECT @@sql_mode as sql_mode")[0]->sql_mode;
    echo "<p>Current SQL Mode: <strong>{$sqlMode}</strong></p>";
    
    // Menonaktifkan ONLY_FULL_GROUP_BY untuk sesi ini
    DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    
    // Memeriksa apakah berhasil
    $newSqlMode = DB::select("SELECT @@sql_mode as sql_mode")[0]->sql_mode;
    echo "<p>Updated SQL Mode: <strong>{$newSqlMode}</strong></p>";
    
    // Menjalankan query yang sama dengan OrderController
    echo "<h3>Testing OrderController Query:</h3>";
    
    try {
        $whereClause = "";
        $limit = 10;
        $offset = 0;
        
        // Count total orders
        $totalOrders = DB::table('pesanan')->count();
        echo "<p>Total orders: {$totalOrders}</p>";
        
        if ($totalOrders > 0) {
            // Get orders with joins
            $query = "SELECT p.id, p.tanggal, p.total_harga, p.status, p.nama_barang,
                          GROUP_CONCAT(DISTINCT pi.produk_id) as product_ids,
                          GROUP_CONCAT(DISTINCT pr.gambar) as product_images
                      FROM pesanan p
                      LEFT JOIN pesanan_item pi ON p.id = pi.pesanan_id
                      LEFT JOIN produk pr ON pi.produk_id = pr.id
                      GROUP BY p.id, p.tanggal, p.total_harga, p.status, p.nama_barang
                      ORDER BY p.tanggal DESC LIMIT $limit OFFSET $offset";
            
            $orders = DB::select($query);
            
            if (count($orders) > 0) {
                echo "<p style='color:green'>Query executed successfully! Found " . count($orders) . " orders.</p>";
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Nama Barang</th><th>Product IDs</th><th>Images</th></tr>";
                
                foreach ($orders as $order) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($order->id ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($order->tanggal ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($order->total_harga ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($order->status ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($order->nama_barang ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($order->product_ids ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($order->product_images ?? 'NULL') . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                echo "<p>These are the orders that should appear in the admin panel.</p>";
                echo "<p>Admin Panel URL: <a href='/admin/orders' target='_blank'>/admin/orders</a></p>";
            } else {
                echo "<p style='color:red'>No orders found with the complex query.</p>";
                
                // Try a simpler query
                $orders = DB::table('pesanan')->orderBy('id', 'desc')->limit(10)->get();
                
                if (count($orders) > 0) {
                    echo "<p>Found " . count($orders) . " orders with a simpler query.</p>";
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
                    
                    // Check pesanan_item table
                    $items = DB::table('pesanan_item')->orderBy('id', 'desc')->limit(10)->get();
                    
                    if (count($items) > 0) {
                        echo "<p>Found " . count($items) . " items in pesanan_item table.</p>";
                    } else {
                        echo "<p style='color:red'>No items found in pesanan_item table. This is why orders don't appear correctly in admin panel.</p>";
                        
                        // Check detail_pesanan table
                        $details = DB::table('detail_pesanan')->orderBy('id', 'desc')->limit(10)->get();
                        
                        if (count($details) > 0) {
                            echo "<p>Found " . count($details) . " items in detail_pesanan table.</p>";
                            echo "<p>The problem is that data is being saved to detail_pesanan but not to pesanan_item.</p>";
                            
                            // Offer to fix the issue
                            echo "<h3>Fix the Issue:</h3>";
                            echo "<p>You can fix this issue by copying data from detail_pesanan to pesanan_item:</p>";
                            echo "<a href='fix_pesanan_item.php' class='btn btn-primary'>Fix Pesanan Item Table</a>";
                        } else {
                            echo "<p style='color:red'>No items found in detail_pesanan table either.</p>";
                        }
                    }
                } else {
                    echo "<p style='color:red'>No orders found with simple query either.</p>";
                }
            }
        } else {
            echo "<p style='color:red'>No orders found in the database.</p>";
            echo "<p>Try making a test order first:</p>";
            echo "<a href='test_checkout.php' class='btn btn-primary'>Create Test Order</a>";
        }
    } catch (\Exception $queryEx) {
        echo "<p style='color:red'>Query Error: " . $queryEx->getMessage() . "</p>";
    }
    
} catch (\Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
