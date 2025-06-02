<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Pelanggan::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('telp', 'like', "%$search%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->input('sort_field', 'idpelanggan');
            $sortDirection = $request->input('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            $pelanggans = $query->get();

            return response()->json([
                'success' => true,
                'data' => $pelanggans->makeHidden(['password']),
                'count' => $pelanggans->count(),
                'message' => $pelanggans->isEmpty() ? 'No customers found' : 'Customers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:pelanggans,email',
            'password' => 'required|string|min:8|confirmed',
            'alamat' => 'nullable|string|max:255',
            'telp' => 'required|string|max:20|unique:pelanggans,telp',
            'status' => 'sometimes|in:aktif,nonaktif'
        ], [
            'email.unique' => 'Email already registered',
            'telp.unique' => 'Phone number already registered',
            'password.confirmed' => 'Password confirmation does not match'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pelanggan = Pelanggan::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'status' => $request->status ?? 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => $pelanggan->makeHidden(['password']),
                'message' => 'Customer created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pelanggan->makeHidden(['password']),
                'message' => 'Customer retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nama' => 'sometimes|string|max:100',
                'email' => [
                    'sometimes',
                    'email',
                    'max:100',
                    Rule::unique('pelanggans')->ignore($id, 'idpelanggan')
                ],
                'password' => 'sometimes|string|min:8|confirmed',
                'alamat' => 'sometimes|string|max:255',
                'telp' => [
                    'sometimes',
                    'string',
                    'max:20',
                    Rule::unique('pelanggans')->ignore($id, 'idpelanggan')
                ],
                'status' => 'sometimes|in:aktif,nonaktif'
            ], [
                'email.unique' => 'Email already registered',
                'telp.unique' => 'Phone number already registered',
                'password.confirmed' => 'Password confirmation does not match'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];
            $fields = ['nama', 'email', 'alamat', 'telp', 'status'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if (!empty($updateData)) {
                $pelanggan->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => $pelanggan->makeHidden(['password']),
                'message' => 'Customer updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            $pelanggan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $pelanggan = Pelanggan::withTrashed()->findOrFail($id);
            $pelanggan->restore();

            return response()->json([
                'success' => true,
                'data' => $pelanggan->makeHidden(['password']),
                'message' => 'Customer restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
