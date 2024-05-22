<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Inspection;
use App\Models\Investigation;
use App\Models\MainAudit;
use App\Models\Organisation;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $actions = Action::where('is_del', 'no');

            $actions = $actions->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->count();

            $investigation = Investigation::where('is_del', 'no');

            $investigation = $investigation->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->count();

            $inspection = Inspection::where('is_del', 'no');

            $inspection = $inspection->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                    $query->orWhere('recipient_organization_id', $organization->id);
                }
            })->count();

            $report = MainAudit::where('is_del', 'no');

            $report = $report->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                    $query->orWhere('recipient_organization_id', $organization->id);
                }
            })->count();

            // $converted_actions = arrayKeysToCamelCase($actions);

            return successResponse('Action Fetched Successfully', [
                'actions' => $actions,
                'investigation' => $investigation,
                'inspection' => $inspection,
                'report' => $report,
            ]);

        } catch (\Throwable $th) {
            logger($th);
            return successResponse('Action Fetched Successfully', []);

        }
    }
}
