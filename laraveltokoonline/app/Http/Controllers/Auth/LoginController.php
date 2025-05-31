<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Log untuk debugging
            \Log::info('Login attempt for email: ' . $request->email);
            \Log::info('Request data: ' . json_encode($request->all()));

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return response()->json([
                    'success' => true
                ]);
            }

            // Coba login dengan username jika email gagal
            if (Auth::attempt(['username' => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();

                return response()->json([
                    'success' => true
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Email atau password salah'
            ]);
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
