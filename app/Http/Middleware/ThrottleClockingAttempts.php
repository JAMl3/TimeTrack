<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleClockingAttempts
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->ip() . '|' . $request->input('employee_number', '');

        if ($this->limiter->tooManyAttempts($key, 5)) { // 5 attempts
            $seconds = $this->limiter->availableIn($key);
            return response()->json([
                'error' => 'Too many attempts. Please try again in ' . $seconds . ' seconds.'
            ], 429);
        }

        $this->limiter->hit($key, 60); // Reset after 60 seconds

        return $next($request);
    }
}
