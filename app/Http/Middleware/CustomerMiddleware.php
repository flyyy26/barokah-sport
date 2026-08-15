<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        if (!auth('customer')->check()) {
            return redirect()
                ->route('customer.login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }

        if (!auth('customer')->user()->is_active) {
            auth('customer')->logout();

            return redirect()
                ->route('customer.login')
                ->with(
                    'error',
                    'Akun Anda sedang tidak aktif.'
                );
        }

        return $next($request);
    }
}