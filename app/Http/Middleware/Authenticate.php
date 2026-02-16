<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Check if the request is for admin panel
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('filament.admin.auth.login');
            }

            // Check if the request is for student area
            if ($request->is('student/*')) {
                return route('student.login');
            }
        }

        return null;
    }
}
