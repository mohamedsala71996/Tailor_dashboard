<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {

        if (! $request->expectsJson()) {
            if (Request::segment(2) == 'dashboard' || Request::segment(1) == 'dashboard'){
                return route('dashboard.adminlogin');
            }
            return route('dashboard.adminlogin');
        }
    }
}
