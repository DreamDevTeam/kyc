<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckHash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!request('hash')) {
            return response()->view('errors.404', [], 404);
        }

        if(!DB::table('links')->where('hash', request('hash'))->exists()){
            return response()->view('errors.404', [], 404);
        }

        if(DB::table('links')->where('hash', request('hash'))->first()->is_registered){
            return response()->view('errors.used');
        }

        return $next($request);
    }
}
