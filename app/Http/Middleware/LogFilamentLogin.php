<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogFilamentLogin
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('*/login') && $request->isMethod('post') && auth()->check()) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'panel' => $this->determinePanel($request),
                ])
                ->log('logged_in');
        }

        return $response;
    }

    private function determinePanel(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, 'admin')) {
            return 'admin';
        }

        if (str_contains($path, 'github')) {
            return 'github';
        }

        return 'unknown';
    }
}
