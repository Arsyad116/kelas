<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Tampilkan halaman orders dan handle form submissions
     */
    public function index(Request $request)
    {
        // Koneksi ke database
        $conn = DB::connection()->getPdo();

        // Initialize message variables
        $message = null;
        $messageType = null;

        // Handle order status update
        if ($request->has('update_status') && $request->has('order_id')) {
            $orderId = intval($request->order_id);
            $status = $request->status;

            try {
                DB::table('pesanan')
                    ->where('id', $orderId)
                    ->update(['status' => $status]);

                $message = "Status pesanan berhasil diperbarui!";
                $messageType = "success";
            } catch (\Exception $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        // Handle order deletion
        if ($request->has('hapus')) {
            $orderId = intval($request->hapus);

            try {
                // First delete records from detail_pesanan
                DB::table('detail_pesanan')
                    ->where('id_pesanan', $orderId)
                    ->delete();

                // Then check if there are order items to delete
                DB::table('pesanan_item')
                    ->where('pesanan_id', $orderId)
                    ->delete();

                // Then delete the order
                DB::table('pesanan')
                    ->where('id', $orderId)
                    ->delete();

                $message = "Pesanan berhasil dihapus!";
                $messageType = "success";
            } catch (\Exception $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        // Get orders with pagination
        $page = $request->has('page') ? intval($request->page) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = $request->has('search') ? $request->search : '';

        // Build the WHERE clause for search
        $whereClause = '';
        $whereBindings = [];
        if (!empty($search)) {
            $whereClause = "WHERE
                p.id LIKE ? OR
                p.status LIKE ?";
            $whereBindings = ["%$search%", "%$search%"];
        }

        // Count total orders for pagination
        $totalQuery = "SELECT COUNT(*) as total FROM pesanan p " . $whereClause;
        $stmt = $conn->prepare($totalQuery);
        if (!empty($whereBindings)) {
            $stmt->execute($whereBindings);
        } else {
            $stmt->execute();
        }
        $totalRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalOrders = $totalRow['total'];
        $totalPages = ceil($totalOrders / $limit);

        // Get orders for current page, joining with pesanan_item and produk to get product images
        // Tambahkan semua kolom yang diselect ke dalam GROUP BY untuk menghindari error ONLY_FULL_GROUP_BY
        $query = "SELECT p.id, p.tanggal, p.total_harga, p.status, p.nama_barang,
                      GROUP_CONCAT(DISTINCT pi.produk_id) as product_ids,
                      GROUP_CONCAT(DISTINCT pr.gambar) as product_images
                  FROM pesanan p
                  LEFT JOIN pesanan_item pi ON p.id = pi.pesanan_id
                  LEFT JOIN produk pr ON pi.produk_id = pr.id
                  " . $whereClause . "
                  GROUP BY p.id, p.tanggal, p.total_harga, p.status, p.nama_barang
                  ORDER BY p.tanggal DESC LIMIT $limit OFFSET $offset";

        $stmt = $conn->prepare($query);
        if (!empty($whereBindings)) {
            $stmt->execute($whereBindings);
        } else {
            $stmt->execute();
        }
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Pass all variables to the view
        return view('admin.orders', [
            'result' => $result,
            'totalOrders' => $totalOrders,
            'totalPages' => $totalPages,
            'page' => $page,
            'search' => $search,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }
}
