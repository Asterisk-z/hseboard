<?php

namespace App\Http\Middleware;

use App\Models\AuditType;
use App\Models\Feature;
use App\Models\MainAudit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureAccessAuditMiddleware
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

        $feature = Feature::HSE_AUDIT_INTERNAL;
        $type = null;
        $typeField = 'INTERNAL';

        if ($audit_type = AuditType::where('id', $request->audit_type_id)->first()) {
            $type = $audit_type->code;
        }

        if ($request->organization_id && $request->recipient_organization_id) {

            if ($request->organization_id !== $request->recipient_organization_id) {
                $typeField = "EXTERNAL";
            }
        }

        if (!$type) {

            if ($main_audit = MainAudit::where('uuid', $request->main_audit_id)->first()) {
                $type = $main_audit->audit_type->code;
                if ($main_audit->is_internal) {
                    $typeField = "INTERNAL";
                } else {
                    $typeField = "EXTERNAL";
                }

            }

        }

        $feature = strtolower("{$type}_AUDIT_{$typeField}");

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
