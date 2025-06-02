<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:pelanggans',
            'password' => 'required|string|min:8|confirmed',
            'alamat' => 'required|string',
            'telp' => 'required|string|max:20',
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
                'status' => 'aktif',
                'api_token' => Str::random(60),
            ]);

            return response()->json([
                'success' => true,
                'data' => $pelanggan->makeHidden(['password']),
                'token' => $pelanggan->api_token,
                'message' => 'Registrasi berhasil'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan = Pelanggan::where('email', $request->email)->first();

        if (!$pelanggan || !Hash::check($request->password, $pelanggan->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        if (!$pelanggan->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif'
            ], 403);
        }

        $pelanggan->api_token = Str::random(60);
        $pelanggan->save();

        return response()->json([
            'success' => true,
            'data' => $pelanggan->makeHidden(['password']),
            'token' => $pelanggan->api_token,
            'message' => 'Login berhasil'
        ]);
    }

    public function logout(Request $request)
    {
        $pelanggan = $request->user();
        $pelanggan->api_token = null;
        $pelanggan->save();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->makeHidden(['password']),
            'message' => 'Data pengguna'
        ]);
    }
}
