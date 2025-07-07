<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureDownloadMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Validasi user login
        if (!auth()->check()) {
            abort(401, 'Authentication required');
        }

        // Validasi user aktif
        if (!auth()->user()->is_active) {
            abort(403, 'Account is not active');
        }

        // Validasi permission download
        if (!auth()->user()->hasPermission('view_any')) {
            abort(403, 'Insufficient permissions');
        }

        return $next($request);
    }
}
