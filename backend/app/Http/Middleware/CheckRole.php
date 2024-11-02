<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        Log::info("CheckRole middleware executed for roles: $roles");
        $rolesArray = explode('|', $roles);

        if (!Auth::check() || !in_array(Auth::user()->role->role_name, $rolesArray)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}