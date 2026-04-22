<?php
// app/Http/Middleware/EnsureAccountType.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountType
{
    public function handle(Request $request, Closure $next, string ...$types): mixed
    {
        if (!in_array(auth()->user()?->account_type, $types)) {
            abort(403, 'Access denied.');
        }
        return $next($request);
    }
}