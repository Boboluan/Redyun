<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Redirect;


class CheckLogin
{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $result = request()->session()->has('user');
        if (!$result) {
            return Redirect::to('/admin/login/login');
        }else{
            return $next($request);
        }
    }


}
