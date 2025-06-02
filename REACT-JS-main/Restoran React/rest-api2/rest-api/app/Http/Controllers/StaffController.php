<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use \Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            }

            // Filter by role
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->input('sort_field', 'id');
            $sortDirection = $request->input('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            $staffs = $query->get();

            return response()->json([
                'success' => true,
                'data' => $staffs->makeHidden(['password']),
                'count' => $staffs->count(),
                'message' => $staffs->isEmpty() ? 'No staff found' : 'Staff retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,kasir'
        ], [
            'email.unique' => 'Email already registered',
            'password.confirmed' => 'Password confirmation does not match'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $staff = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'aktif',
                'api_token' => Str::random(60),
            ]);

            return response()->json([
                'success' => true,
                'data' => $staff->makeHidden(['password']),
                'message' => 'Staff created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $staff = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $staff->makeHidden(['password']),
                'message' => 'Staff retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $staff = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:100',
                'email' => [
                    'sometimes',
                    'email',
                    'max:100',
                    Rule::unique('users')->ignore($id)
                ],
                'password' => 'sometimes|string|min:8|confirmed',
                'role' => 'sometimes|in:admin,manager,kasir',
                'status' => 'sometimes|in:active,inactive'
            ], [
                'email.unique' => 'Email already registered',
                'password.confirmed' => 'Password confirmation does not match'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];
            $fields = ['name', 'email', 'role', 'status'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if (!empty($updateData)) {
                $staff->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => $staff->makeHidden(['password']),
                'message' => 'Staff updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $staff = User::findOrFail($id);
            $staff->delete();

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $staff = User::withTrashed()->findOrFail($id);
            $staff->restore();

            return response()->json([
                'success' => true,
                'data' => $staff->makeHidden(['password']),
                'message' => 'Staff restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
