<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $feature)
    {

        logger($feature);

        abort(Response::HTTP_UPGRADE_REQUIRED, 'You dont have access to this feature');

        if (!$organization = auth()->user()->activeOrg) {
            abort(Response::HTTP_UPGRADE_REQUIRED, "You don't have access to this feature");
        }
        logger($organization->privilege);
        // if (auth()->user()->email_verified_at) {
        return $next($request);
        // }

    }
}
