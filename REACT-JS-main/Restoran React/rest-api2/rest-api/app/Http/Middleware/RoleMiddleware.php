<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if ($user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Required role: ' . $role
            ], 403);
        }

        return $next($request);
    }
}
