<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEventAccess
{
    /**
     * Restrict client users to their linked event when a route has {event}.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $event = $request->route('event');

        if ($user && $event instanceof Event && ! $user->canAccessEvent($event)) {
            abort(404);
        }

        return $next($request);
    }
}
