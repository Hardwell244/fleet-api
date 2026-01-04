<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyOwnership
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lista de rotas que devem filtrar por company_id automaticamente
        $filterableRoutes = [
            'v1/vehicles',
            'v1/drivers',
            'v1/maintenances',
            'v1/deliveries',
        ];

        // Verifica se é uma rota que precisa de filtro
        $needsFilter = false;
        foreach ($filterableRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                $needsFilter = true;
                break;
            }
        }

        // Se for listagem (GET sem ID), adiciona filtro de company_id automaticamente
        if ($needsFilter && $request->isMethod('GET') && !$request->route('id')) {
            $request->merge(['company_id' => auth()->user()->company_id]);
        }

        return $next($request);
    }
}
