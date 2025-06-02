<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Order::with(['pelanggan', 'details']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by customer
            if ($request->has('idpelanggan')) {
                $query->where('idpelanggan', $request->idpelanggan);
            }

            // Date range filter
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Sorting
            $sortField = $request->input('sort_field', 'idorder');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            $orders = $query->get();

            return response()->json([
                'success' => true,
                'data' => $orders,
                'count' => $orders->count(),
                'message' => $orders->isEmpty() ? 'No orders found' : 'Orders retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idpelanggan' => 'required|exists:pelanggans,idpelanggan',
            'total' => 'required|numeric|min:0',
            'status' => 'sometimes|in:menunggu,diproses,selesai,dibatalkan',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.menu' => 'required|string|max:100',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.subtotal' => 'required|numeric|min:0',
            'details.*.catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $orderData = $request->only(['idpelanggan', 'total', 'status', 'catatan']);
            $orderData['status'] = $orderData['status'] ?? 'menunggu';

            $order = Order::create($orderData);

            foreach ($request->details as $detail) {
                OrderDetail::create([
                    'idorder' => $order->idorder,
                    'menu' => $detail['menu'],
                    'harga' => $detail['harga'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal'],
                    'catatan' => $detail['catatan'] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $order->load(['pelanggan', 'details']),
                'message' => 'Order created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with(['pelanggan', 'details'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'idpelanggan' => 'sometimes|exists:pelanggans,idpelanggan',
                'total' => 'sometimes|numeric',
                'status' => 'sometimes|in:menunggu,diproses,selesai,dibatalkan',
                'catatan' => 'sometimes|nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $updateData = [];
            $fields = ['idpelanggan', 'total', 'status', 'catatan'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if (!empty($updateData)) {
                $order->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => $order->load(['pelanggan', 'details']),
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:menunggu,diproses,selesai,dibatalkan'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $order->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->details()->delete();
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
