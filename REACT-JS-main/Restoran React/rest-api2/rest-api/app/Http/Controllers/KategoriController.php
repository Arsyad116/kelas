<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Kategori::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kategori', 'like', "%$search%")
                        ->orWhere('deskripsi', 'like', "%$search%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->input('sort_field', 'idkategori');
            $sortDirection = $request->input('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Get all results (no pagination)
            $kategories = $query->get();

            return response()->json([
                'success' => true,
                'data' => $kategories,
                'count' => $kategories->count(),
                'message' => $kategories->isEmpty() ? 'No categories found' : 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:100|unique:kategoris,kategori',
            'deskripsi' => 'nullable|string|max:255',
            'status' => 'sometimes|in:aktif,nonaktif'
        ], [
            'kategori.unique' => 'Category name already exists'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kategori = Kategori::create([
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'status' => $request->status ?? 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => $kategori,
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kategori = Kategori::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $kategori,
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kategori = Kategori::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'kategori' => 'sometimes|string|max:100|unique:kategoris,kategori,' . $id . ',idkategori',
                'deskripsi' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|in:aktif,nonaktif'
            ], [
                'kategori.unique' => 'Category name already exists'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];
            if ($request->has('kategori')) {
                $updateData['kategori'] = $request->kategori;
            }
            if ($request->has('deskripsi')) {
                $updateData['deskripsi'] = $request->deskripsi;
            }
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            if (!empty($updateData)) {
                $kategori->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => $kategori,
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = Kategori::findOrFail($id);

            if ($kategori->menus()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category because it has associated menu items'
                ], 400);
            }

            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
