<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsRegular
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('warga.login');
        }

        if ($request->user()->role !== 'user') {
            abort(403, 'Halaman ini khusus warga.');
        }

        return $next($request);
    }
}