<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Pelanggan;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        try {
            if ($this->authenticate($request, $guards)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            Log::error('Authentication error', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'error' => $e->getMessage(),
                'trace' => Str::limit($e->getTraceAsString(), 200)
            ]);
        }

        $this->throwAuthenticationException($request, $guards);
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                auth()->shouldUse($guard);
                return true;
            }
        }

        // Coba autentikasi token untuk staff atau pelanggan
        $token = $request->bearerToken();

        if ($token) {
            // Cek sebagai staff (user)
            $user = User::where('api_token', $token)->first();
            if ($user) {
                auth()->setUser($user);
                return true;
            }

            // Cek sebagai pelanggan
            $pelanggan = Pelanggan::where('api_token', $token)->first();
            if ($pelanggan) {
                auth()->setUser($pelanggan);
                return true;
            }
        }

        return false;
    }

    protected function throwAuthenticationException($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }

    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }
    }
}
