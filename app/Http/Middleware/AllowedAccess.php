<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllowedAccess {
    public function handle(Request $request, Closure $next) {
        if(Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1){
            return $next($request);
        }     
        abort(403);   
    }
}
