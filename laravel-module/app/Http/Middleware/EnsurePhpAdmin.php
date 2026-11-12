<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhpAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Read the existing PHP application's session.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('PHPSESSID');
            session_start();
        }

        // Redirect users who are not logged in.
        if (empty($_SESSION['user_id'])) {
            return redirect('http://localhost/EventTicketing/public/login.php');
        }

        // Only administrators can access this module.
        if (($_SESSION['role'] ?? '') !== 'admin') {
            abort(403, 'Access denied. Admin privileges are required.');
        }

        return $next($request);
    }
}
