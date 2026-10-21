<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a Server-Timing header (boot, database and total time in PHP, and the number of queries) so the live site can be
 * measured from the browser's network panel or curl. Opening the database connection is not a query, so it counts in the total only.
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $boot = (microtime(true) - $start) * 1000;
        $queries = 0;
        $database = 0.0;

        DB::listen(function (QueryExecuted $query) use (&$queries, &$database) {
            $queries++;
            $database += $query->time;
        });

        $response = $next($request);

        $response->headers->set('Server-Timing', sprintf(
            'boot;dur=%.1f;desc="Laravel boot", db;dur=%.1f;desc="%d %s", total;dur=%.1f;desc="Total in PHP"',
            $boot,
            $database,
            $queries,
            $queries === 1 ? 'query' : 'queries',
            (microtime(true) - $start) * 1000,
        ));

        return $response;
    }
}
