<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class authAdm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->session()->get('cargo') == 'Administrador') {
            return $next($request);
        }

        return response()->json(['message' => 'Usuário não autorizado'], 401);
    }
}
