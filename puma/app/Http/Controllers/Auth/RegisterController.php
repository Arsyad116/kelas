<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ]);
        }

        try {
            // Log untuk debugging
            \Log::info('Register attempt for username: ' . $request->username . ', email: ' . $request->email);

            // Hash password
            $hashedPassword = Hash::make($request->password);
            \Log::info('Password hashed successfully');

            // Coba gunakan kolom username daripada name
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $hashedPassword;
            $user->save();

            \Log::info('User registered successfully with ID: ' . $user->id);
        } catch (\Exception $e) {
            \Log::error('Register error: ' . $e->getMessage());
            \Log::error('Register error trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
