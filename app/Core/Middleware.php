<?php

namespace App\Core;

class Middleware
{
    /**
     * Runs a middleware string like "auth" or "role:super_admin,property_manager".
     */
    public static function run(string $middleware): void
    {
        [$name, $args] = array_pad(explode(':', $middleware, 2), 2, '');

        switch ($name) {
            case 'auth':
                if (!Auth::check()) {
                    header('Location: /login');
                    exit;
                }
                break;

            case 'guest':
                if (Auth::check()) {
                    header('Location: ' . (Auth::role() === 'tenant' ? '/portal' : '/dashboard'));
                    exit;
                }
                break;

            case 'role':
                $roles = array_filter(explode(',', $args));
                if (!Auth::hasRole($roles)) {
                    http_response_code(403);
                    View::render('errors.403');
                    exit;
                }
                break;
        }
    }
}
