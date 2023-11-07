<?php

namespace App\Http\Middleware;

use Closure;

class PublicGallery
{
    public function handle($request, Closure $next)
    {
        if (env('FEATURE_PUBLIC_GALLERY', false)) {
            return $next($request);
        }
        return response('fail', 404);
    }
}
