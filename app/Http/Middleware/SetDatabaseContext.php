<?php

namespace App\Http\Middleware;

use App\Support\DbContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs on every request (wrapped around all routes in routes/web.php).
 * Passes the logged-in user's role and id to PostgreSQL for row-level security.
 */
class SetDatabaseContext
{
    public function handle(Request $request, Closure $next): Response
    {
        DbContext::apply(DB::connection(), $request->user());

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Leave nothing behind on a reused connection
        try {
            DbContext::clear(DB::connection());
        } catch (\Throwable) {
            // connection already closed: nothing to clear
        }
    }
}
