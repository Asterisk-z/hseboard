<?php

namespace App\Http\Middleware;

use App\Models\PermitToWork;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureAccessPermitToWorkMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // INTERNAL_PERMIT_TO_WORK
        // EXTERNAL_PERMIT_TO_WORK

        $typeField = 'INTERNAL';

        if ($request->organization_id && $request->recipient_organization_id) {

            if ($request->organization_id !== $request->recipient_organization_id) {
                $typeField = "EXTERNAL";
            }
        }

        if ($request->permit_id) {

            if ($permit = PermitToWork::where('uuid', $request->permit_id)->first()) {

                if ($permit->is_internal) {
                    $typeField = "INTERNAL";
                } else {
                    $typeField = "EXTERNAL";
                }

            }

        }

        $feature = strtolower("{$typeField}_PERMIT_TO_WORK");

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
