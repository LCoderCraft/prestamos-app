<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario NO es admin, lo mandamos al dashboard normal
        if (Auth::user()->role !== 'admin') {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}