<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVendorApproved
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();

        if ($u && strtolower($u->rol ?? '') === 'vendedor' && $u->vendor_status !== 'approved') {
            return response()->json([
                'message' => 'Tu cuenta de vendedor aún no está aprobada.'
            ], 403);
        }

        return $next($request);
    }
}
