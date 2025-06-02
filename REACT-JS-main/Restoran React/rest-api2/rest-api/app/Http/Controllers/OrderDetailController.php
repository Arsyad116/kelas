<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = OrderDetail::query();

            if ($request->has('idorder')) {
                $query->where('idorder', $request->idorder);
            }

            $orderDetails = $query->get();

            return response()->json([
                'success' => true,
                'data' => $orderDetails,
                'count' => $orderDetails->count(),
                'message' => $orderDetails->isEmpty() ? 'No order details found' : 'Order details retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idorder' => 'required|exists:orders,idorder',
            'menu' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $orderDetail = OrderDetail::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $orderDetail,
                'message' => 'Order detail created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($orderId, $id)
    {
        try {
            $detail = OrderDetail::where('idorder', $orderId)->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $detail,
                'message' => 'Order detail retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order detail not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $orderId, $id)
    {
        try {
            $detail = OrderDetail::where('idorder', $orderId)
                ->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'menu' => 'sometimes|string|max:100',
                'harga' => 'sometimes|numeric',
                'jumlah' => 'sometimes|integer',
                'subtotal' => 'sometimes|numeric',
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
            $fields = ['menu', 'harga', 'jumlah', 'subtotal', 'catatan'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if (!empty($updateData)) {
                $detail->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => $detail,
                'message' => 'Order detail updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($orderId, $id)
    {
        try {
            $detail = OrderDetail::where('idorder', $orderId)
                ->findOrFail($id);
            $detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order detail deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
