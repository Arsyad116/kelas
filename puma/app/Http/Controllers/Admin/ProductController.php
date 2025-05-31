<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk
     */
    public function index()
    {
        try {
            $products = Product::orderBy('id', 'desc')->get();
        } catch (\Exception $e) {
            $products = [];
        }
        
        return view('admin.products.index', [
            'products' => $products
        ]);
    }
    
    /**
     * Tampilkan form untuk membuat produk baru
     */
    public function create()
    {
        return view('admin.products.create');
    }
    
    /**
     * Simpan produk baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Upload gambar
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        } else {
            $imageName = 'default.jpg';
        }
        
        // Simpan produk
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->image = $imageName;
        $product->stock = $request->stock ?? 0;
        $product->category = $request->category ?? 'Uncategorized';
        $product->is_active = $request->has('is_active') ? 1 : 0;
        $product->save();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }
    
    /**
     * Tampilkan form untuk mengedit produk
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        return view('admin.products.edit', [
            'product' => $product
        ]);
    }
    
    /**
     * Update produk
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $product = Product::findOrFail($id);
        
        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika bukan default
            if ($product->image != 'default.jpg' && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }
        
        // Update produk
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock ?? 0;
        $product->category = $request->category ?? 'Uncategorized';
        $product->is_active = $request->has('is_active') ? 1 : 0;
        $product->save();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }
    
    /**
     * Hapus produk
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Hapus gambar jika bukan default
        if ($product->image != 'default.jpg' && file_exists(public_path('images/' . $product->image))) {
            unlink(public_path('images/' . $product->image));
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}
