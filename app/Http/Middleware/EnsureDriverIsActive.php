<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class EnsureDriverIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->type !== 'driver' || !$user->is_active) {
            return ApiResponse::SendRespond( 403,'هذا السائق غير مفعل أو غير مصرح له', []);
        }

        return $next($request);
    }
}
