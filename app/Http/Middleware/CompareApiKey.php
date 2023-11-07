<?php

namespace App\Http\Middleware;

use Closure;

class CompareApiKey
{
    public function handle($request, Closure $next)
    {
        $API_KEY = request()->header('key');
        if (!empty(env('API_KEY')) && $API_KEY == env('API_KEY')) {
            return $next($request);
        }
        return response('fail', 401);
    }
}
