<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has teacher role
        if ($user->role !== 'teacher') {
            return redirect()->route('login')->with('error', 'Access denied. Teacher account required.');
        }

        // Check if teacher record exists
        if (!$user->teacher) {
            return redirect()->route('login')->with('error', 'Teacher profile not found.');
        }

        return $next($request);
    }
} 