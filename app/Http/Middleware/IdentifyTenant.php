<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();

                // 🔥 VERSIÓN SIMPLIFICADA - SIN SUPER ADMIN
                if ($user->tenant_id) {
                    $tenant = $user->tenant;

                    if ($tenant) {
                        app()->instance('currentTenant', $tenant);
                        view()->share('currentTenant', $tenant);
                    }
                }
            }
        } catch (\Exception $e) {
            // Si hay error, continuar sin tenant
            report($e);
        }

        return $next($request);
    }
}