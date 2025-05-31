<?php
// Memuat autoloader Laravel
require __DIR__.'/../vendor/autoload.php';

// Memuat aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Menggunakan facade DB
use Illuminate\Support\Facades\DB;

// Menampilkan informasi koneksi database
echo "<h2>Laravel SQL Mode Fix</h2>";

try {
    // Mendapatkan konfigurasi database
    $connection = config('database.default');
    $config = config('database.connections.' . $connection);
    
    echo "<p>Default Connection: <strong>{$connection}</strong></p>";
    echo "<p>Database Name: <strong>{$config['database']}</strong></p>";
    
    // Memeriksa SQL mode saat ini
    $sqlMode = DB::select("SELECT @@sql_mode as sql_mode")[0]->sql_mode;
    echo "<p>Current SQL Mode: <strong>{$sqlMode}</strong></p>";
    
    // Memeriksa apakah ONLY_FULL_GROUP_BY diaktifkan
    if (strpos($sqlMode, 'ONLY_FULL_GROUP_BY') !== false) {
        echo "<p style='color:red'>ONLY_FULL_GROUP_BY is enabled, which may cause issues with GROUP BY queries.</p>";
        
        // Menonaktifkan ONLY_FULL_GROUP_BY untuk sesi ini
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        
        // Memeriksa apakah berhasil
        $newSqlMode = DB::select("SELECT @@sql_mode as sql_mode")[0]->sql_mode;
        echo "<p>Updated SQL Mode: <strong>{$newSqlMode}</strong></p>";
        
        if (strpos($newSqlMode, 'ONLY_FULL_GROUP_BY') === false) {
            echo "<p style='color:green'>Successfully disabled ONLY_FULL_GROUP_BY for this session.</p>";
        } else {
            echo "<p style='color:red'>Failed to disable ONLY_FULL_GROUP_BY.</p>";
        }
    } else {
        echo "<p style='color:green'>ONLY_FULL_GROUP_BY is not enabled, which is good for the current query.</p>";
    }
    
    // Menambahkan instruksi untuk memperbaiki secara permanen
    echo "<h3>Permanent Fix:</h3>";
    echo "<p>To permanently fix this issue, you can add the following code to your AppServiceProvider.php file:</p>";
    echo "<pre>
public function boot()
{
    \$this->app->make('db')->connection()->statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,\"ONLY_FULL_GROUP_BY\",\"\"));');
}
</pre>";
    
    // Mencoba menjalankan query OrderController
    echo "<h3>Testing OrderController Query:</h3>";
    
    try {
        $whereClause = "";
        $limit = 10;
        $offset = 0;
        
        // Count total orders
        $totalOrders = DB::table('pesanan')->count();
        echo "<p>Total orders: {$totalOrders}</p>";
        
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
            echo "<p style='color:green'>Query executed successfully!</p>";
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
        } else {
            echo "<p>No orders found.</p>";
        }
    } catch (\Exception $queryEx) {
        echo "<p style='color:red'>Query Error: " . $queryEx->getMessage() . "</p>";
        
        // Try a simpler query
        echo "<h3>Testing Simple Query:</h3>";
        try {
            $orders = DB::table('pesanan')->orderBy('id', 'desc')->limit(10)->get();
            
            if (count($orders) > 0) {
                echo "<p style='color:green'>Simple query works! Found " . count($orders) . " orders.</p>";
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
                echo "<p>No orders found with simple query.</p>";
            }
        } catch (\Exception $simpleEx) {
            echo "<p style='color:red'>Simple Query Error: " . $simpleEx->getMessage() . "</p>";
        }
    }
    
} catch (\Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
