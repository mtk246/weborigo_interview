<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RegisterDevice;

class AuthXApiKey
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
        if ($request->header('X-API-KEY') == RegisterDevice::where('device_api_key', $request->header('X-API-KEY'))->first()->device_api_key) {
            return $next($request);
        } else {
            return response([
                "status" => false,
                "message" => "unauthorized"
            ]);
        }
    }
}
