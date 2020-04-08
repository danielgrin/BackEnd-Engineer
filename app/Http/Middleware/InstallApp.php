<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Artisan;
use Schema;

class InstallApp
{
    /**
     * Quickly install the app during development with a local SqLite DB.
     * Remove in production.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $db_path = database_path(getenv('DB_FILENAME'));

        if (!file_exists($db_path)) {

            exec('touch ' . $db_path);

        }

        if (!Schema::hasTable('migrations')) {

            Artisan::call('migrate', ["--force" => true]);
        }

        $response = $next($request);

        return $response;
    }
}
