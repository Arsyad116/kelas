<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard admin
     */
    public function index(Request $request)
    {
        // Ambil data produk
        try {
            $result = Product::orderBy('id', 'desc')->get();
            $productCount = $result->count();
        } catch (\Exception $e) {
            $result = collect();
            $productCount = 0;
        }

        // Hitung jumlah banner
        try {
            $bannerCount = Banner::count();
        } catch (\Exception $e) {
            $bannerCount = 0;
        }

        // Handle upload banner
        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            $file = $request->file('banner');
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = $file->getClientOriginalExtension();

            if (in_array(strtolower($extension), $allowedTypes) && $file->getSize() <= 2 * 1024 * 1024) {
                $newFileName = uniqid("banner_", true) . "." . $extension;
                $file->move(public_path('images'), $newFileName);

                // Save to database
                try {
                    Banner::create([
                        'image_path' => $newFileName,
                        'title' => 'Banner ' . date('Y-m-d'),
                        'is_active' => 1
                    ]);

                    $message = "Upload successful! File saved as: " . $newFileName;
                    $messageType = "success";
                } catch (\Exception $e) {
                    $message = "Database Error: " . $e->getMessage();
                    $messageType = "error";
                }
            } else {
                $message = "Error: Only JPG, JPEG, PNG, and GIF files are allowed. Max size 2MB.";
                $messageType = "error";
            }
        }

        // Handle AJAX product update
        if ($request->has('edit_product') && $request->has('id') && $request->ajax()) {
            $id = intval($request->id);
            $nama = $request->nama;
            $harga = intval($request->harga);
            $response = ['success' => false];

            try {
                $product = Product::findOrFail($id);

                // Check if there's a new image
                if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                    $file = $request->file('gambar');
                    $newFileName = uniqid("product_", true) . "." . $file->getClientOriginalExtension();
                    $file->move(public_path('images'), $newFileName);
                    $product->image = $newFileName;
                }

                $product->name = $nama;
                $product->price = $harga;
                $product->save();

                $response['success'] = true;
                return response()->json($response);
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
                return response()->json($response);
            }
        }

        // Handle add product
        if ($request->has('add_product')) {
            $nama = $request->nama;
            $harga = $request->harga;

            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                $file = $request->file('gambar');
                $gambar = uniqid("product_", true) . "." . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $gambar);

                try {
                    Product::create([
                        'name' => $nama,
                        'price' => $harga,
                        'image' => $gambar,
                        'is_active' => 1
                    ]);

                    $message = "Produk berhasil ditambahkan!";
                    $messageType = "success";
                } catch (\Exception $e) {
                    $message = "Error: " . $e->getMessage();
                    $messageType = "error";
                }
            }
        }

        // Handle edit product (non-AJAX)
        if ($request->has('edit_product') && $request->has('id') && !$request->ajax()) {
            $id = $request->id;
            $nama = $request->nama;
            $harga = $request->harga;

            try {
                $product = Product::findOrFail($id);

                // Check if there's a new image
                if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                    $file = $request->file('gambar');
                    $gambar = uniqid("product_", true) . "." . $file->getClientOriginalExtension();
                    $file->move(public_path('images'), $gambar);
                    $product->image = $gambar;
                }

                $product->name = $nama;
                $product->price = $harga;
                $product->save();

                $message = "Produk berhasil diupdate!";
                $messageType = "success";
            } catch (\Exception $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        // Handle delete product
        if ($request->has('hapus')) {
            $id = $request->hapus;

            try {
                $product = Product::findOrFail($id);
                $product->delete();

                $message = "Produk berhasil dihapus!";
                $messageType = "success";
            } catch (\Exception $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        // Pass variables to view
        $data = [
            'result' => $result,
            'productCount' => $productCount,
            'bannerCount' => $bannerCount
        ];

        if (isset($message)) {
            $data['message'] = $message;
            $data['messageType'] = $messageType;
        }

        return view('admin.index', $data);
    }

    /**
     * Get product details for AJAX
     */
    public function getProduct(Request $request)
    {
        if ($request->has('id')) {
            $id = intval($request->id);

            try {
                $product = Product::findOrFail($id);
                return response()->json([
                    'id' => $product->id,
                    'nama' => $product->name,
                    'harga' => $product->price,
                    'gambar' => $product->image
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Product not found']);
            }
        }

        return response()->json(['error' => 'Invalid request']);
    }
}
