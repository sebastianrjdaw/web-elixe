<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'gl' ? 'gl' : 'es';

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
