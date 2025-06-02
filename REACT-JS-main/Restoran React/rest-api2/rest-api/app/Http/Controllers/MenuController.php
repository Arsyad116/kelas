<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Menu::with('kategori');

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('menu', 'like', "%$search%")
                        ->orWhere('deskripsi', 'like', "%$search%");
                });
            }

            // Filter by category
            if ($request->has('idkategori')) {
                $query->where('idkategori', $request->idkategori);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->input('sort_field', 'idmenu');
            $sortDirection = $request->input('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            $menus = $query->get();

            return response()->json([
                'success' => true,
                'data' => $menus,
                'count' => $menus->count(),
                'message' => $menus->isEmpty() ? 'No menus found' : 'Menus retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve menus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu' => 'required|string|max:100|unique:menus,menu',
            'idkategori' => 'required|exists:kategoris,idkategori',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:255',
            'status' => 'sometimes|in:tersedia,habis',
            'gambar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ], [
            'menu.unique' => 'Menu name already exists',
            'idkategori.exists' => 'Category not found',
            'harga.numeric' => 'Price must be a number',
            'harga.min' => 'Price cannot be negative',
            'gambar.image' => 'File must be an image',
            'gambar.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'gambar.max' => 'Image size must be less than 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $menuData = $request->only(['menu', 'idkategori', 'harga', 'deskripsi', 'status']);
            $menuData['status'] = $menuData['status'] ?? 'tersedia';

            if ($request->hasFile('gambar')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->gambar->extension();
                $path = $request->gambar->storeAs('public/images/menu', $imageName);
                $menuData['gambar'] = 'images/menu/' . $imageName;
            }

            $menu = Menu::create($menuData);
            $menu->load('kategori');

            return response()->json([
                'success' => true,
                'data' => $menu,
                'message' => 'Menu created successfully'
            ], 201);
        } catch (\Exception $e) {
            if (isset($menuData['gambar'])) {
                Storage::delete('public/' . $menuData['gambar']);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $menu = Menu::with('kategori')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $menu,
                'message' => 'Menu retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $menu = Menu::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'menu' => [
                    'sometimes',
                    'string',
                    'max:100',
                    Rule::unique('menus')->ignore($id)->whereNull('deleted_at')
                ],
                'idkategori' => 'sometimes|exists:kategoris,idkategori',
                'harga' => 'sometimes|numeric|min:0',
                'deskripsi' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|in:tersedia,habis',
                'gambar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ], [
                'menu.unique' => 'Menu name already exists',
                'idkategori.exists' => 'Category not found',
                'harga.numeric' => 'Price must be a number',
                'harga.min' => 'Price cannot be negative',
                'gambar.image' => 'File must be an image',
                'gambar.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
                'gambar.max' => 'Image size must be less than 2MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];
            $fields = ['menu', 'idkategori', 'harga', 'deskripsi', 'status'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if ($request->hasFile('gambar')) {
                if ($menu->gambar) {
                    Storage::delete('public/' . $menu->gambar);
                }

                $imageName = time() . '_' . uniqid() . '.' . $request->gambar->extension();
                $path = $request->gambar->storeAs('public/images/menu', $imageName);
                $updateData['gambar'] = 'images/menu/' . $imageName;
            }

            if (!empty($updateData)) {
                $menu->update($updateData);
            }

            $menu->load('kategori');

            return response()->json([
                'success' => true,
                'data' => $menu,
                'message' => 'Menu updated successfully'
            ]);
        } catch (\Exception $e) {
            if (isset($updateData['gambar'])) {
                Storage::delete('public/' . $updateData['gambar']);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);

            if ($menu->gambar) {
                Storage::delete('public/' . $menu->gambar);
            }

            $menu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getKategoris()
    {
        try {
            $kategories = Kategori::where('status', 'aktif')->get();

            return response()->json([
                'success' => true,
                'data' => $kategories,
                'message' => 'Active categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
