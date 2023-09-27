<?php

namespace App\Http\Middleware;

use App\Models\Tuser;
use Closure;
use Illuminate\Support\Facades\Auth;

class CanTakePhoto
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('web')->check() && auth()->user()) {
            $tuser = Tuser::find(auth()->user()->id);
            if ($tuser->canTakePhotos()) {
                return $next($request);
            } else {
                return redirect()->route('app.gallery');
            }
                return redirect()->route('login');
        }
    }
}
