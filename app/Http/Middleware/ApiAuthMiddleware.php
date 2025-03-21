<?php

namespace App\Http\Middleware;

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
        // if (!Auth::guard('sanctum')->check()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Unauthorized access. Please provide a valid API token.'
        //     ], 401);
        // }

        // return $next($request);
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. Please provide a valid API token.'
            ], 401);
        }

        // Set the authenticated user for further use in the request
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
