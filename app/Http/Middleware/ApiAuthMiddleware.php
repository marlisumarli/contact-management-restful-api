<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();

        if (!$token || !$user) {
            return response()->json([
                'error' => [
                    'message' => [
                        'Unauthorized'
                    ],
                ],
            ], 401);
        }

        Auth::login($user);

        return $next($request);
    }
}
