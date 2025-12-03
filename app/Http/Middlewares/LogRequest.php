<?php

namespace App\Http\Middlewares;

use App\Domains\Organization\Exceptions\OrganizationCouldNotBeIdentifiedByDomainException;
use App\Models\Domain;
use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $file = fopen(base_path()."/storage/logs/query-logs.log", "a");
        fwrite($file, "\n\n".'[Incoming request: ' . $request->method() . ' ' . $request->url() . "]\n");
        fclose($file);

        Event::listen(QueryExecuted::class, function (QueryExecuted $query) {
            $file = fopen(base_path()."/storage/logs/query-logs.log", "a");
            fwrite($file, "\n[".$query->connectionName."]".now()->toDateTimeString()."\n");
            fwrite($file, $query->toRawSql()."\n".$query->time);
            fclose($file);
        });

        return $next($request);
    }
}
