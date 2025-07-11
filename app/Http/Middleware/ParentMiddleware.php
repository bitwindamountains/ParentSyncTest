<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isParent()) {
            return response()->json(['message' => 'Access denied. Parent privileges required.'], 403);
        }
        return $next($request);
    }
}