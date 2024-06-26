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

        // abort(Response::HTTP_UPGRADE_REQUIRED, 'You dont have access to this feature');
        $allowAccess = false;
        if (!$organization = auth()->user()->activeOrg) {
            abort(Response::HTTP_UPGRADE_REQUIRED, "You are not in an organization");
        }
        $privileges = $organization->privilege;
        foreach ($privileges as $privilege) {
            if ($privilege->feature_code == $feature && $privilege->is_active) {
                $allowAccess = true;
                break;
            }
        }

        if (!$allowAccess) {
            abort(Response::HTTP_UPGRADE_REQUIRED, 'You dont have access to this feature');

        }

        return $next($request);

    }
}
