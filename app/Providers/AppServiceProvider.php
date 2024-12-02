<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Response::macro('withSecurityHeaders', function ($response) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");

            if ($response->headers->has('Set-Cookie')) {
                $response->headers->set('Set-Cookie', $response->headers->get('Set-Cookie') . '; Secure; SameSite=Lax');
            }

            return $response;
        });
    }
}
