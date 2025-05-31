<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Tampilkan halaman customers dan handle form submissions
     */
    public function index(Request $request)
    {
        // Koneksi ke database
        $conn = DB::connection()->getPdo();

        // Initialize message variables
        $message = null;
        $messageType = null;

        // Handle customer deletion
        if ($request->has('hapus')) {
            $customerId = intval($request->hapus);

            try {
                // Delete customer
                DB::table('users')
                    ->where('id', $customerId)
                    ->delete();

                $message = "Customer berhasil dihapus!";
                $messageType = "success";
            } catch (\Exception $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        // Get customers with pagination
        $page = $request->has('page') ? intval($request->page) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = $request->has('search') ? $request->search : '';

        // Build the WHERE clause for search
        $whereClause = '';
        $whereBindings = [];
        if (!empty($search)) {
            $whereClause = "WHERE
                name LIKE ? OR
                email LIKE ?";
            $whereBindings = ["%$search%", "%$search%"];
        }

        // Count total customers for pagination
        $totalQuery = "SELECT COUNT(*) as total FROM users " . $whereClause;
        $stmt = $conn->prepare($totalQuery);
        if (!empty($whereBindings)) {
            $stmt->execute($whereBindings);
        } else {
            $stmt->execute();
        }
        $totalRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalCustomers = $totalRow['total'];
        $totalPages = ceil($totalCustomers / $limit);

        // Get customers for current page
        $query = "SELECT * FROM users " . $whereClause . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
        $stmt = $conn->prepare($query);
        if (!empty($whereBindings)) {
            $stmt->execute($whereBindings);
        } else {
            $stmt->execute();
        }
        $customers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Periksa struktur kolom dan tambahkan kolom yang hilang
        foreach ($customers as &$customer) {
            // Tambahkan kolom 'name' jika tidak ada
            if (!isset($customer['name'])) {
                $customer['name'] = $customer['username'] ?? $customer['nama'] ?? 'Customer ' . $customer['id'];
            }

            // Tambahkan kolom 'created_at' jika tidak ada
            if (!isset($customer['created_at'])) {
                $customer['created_at'] = date('Y-m-d H:i:s');
            }
        }

        // Pass all variables to the view
        return view('admin.customers', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'totalPages' => $totalPages,
            'page' => $page,
            'search' => $search,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }
}
