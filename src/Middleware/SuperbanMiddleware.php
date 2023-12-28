<?php

namespace Emmanuelabraham\Superban\Middleware;

use Closure;
use Illuminate\Http\Request;
use Superban\Drivers\DriverInterface;

class SuperbanMiddleware
{
    protected $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function handle(Request $request, Closure $next, $maxAttempts, $decayMinutes, $banMinutes)
    {
        if ($this->driver->tooManyAttempts($request, $maxAttempts, $decayMinutes)) {
            $this->driver->ban($request, $banMinutes);
            return response()->json([
                'message' => 'You are banned for ' . $banMinutes . ' minutes.'
            ], 403);
        }
        return $next($request);
    }
}