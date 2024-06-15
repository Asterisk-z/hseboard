<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Inspection;
use App\Models\Investigation;
use App\Models\MainAudit;
use App\Models\Observation;
use App\Models\ObservationType;
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

            $actionsTabs = ['pending', 'accepted', 'rejected', 'ongoing', 'canceled', 'completed'];

            $observationTabs = [
                ['name' => 'Unsafe Act', 'type' => ObservationType::UNSAFE_ACT],
                ['name' => 'Unsafe Condition', 'type' => ObservationType::UNSAFE_CONDITION],
                ['name' => 'Near Miss', 'type' => ObservationType::NEAR_MISS],
                ['name' => 'Medical Treatment Case', 'type' => ObservationType::MEDICAL_TREATMENT],
                ['name' => 'Fatality', 'type' => ObservationType::FATALITY],
                ['name' => 'Dangerous Occurrence', 'type' => ObservationType::DANGEROUS_OCCURANCE],
            ];

            $observationChart = [];

            foreach ($observationTabs as $observationTab) {

                $observationCount = Observation::where('organization_id', $organization->id)->where('observation_type_id', $observationTab['type'])->count();
                $observationItem = ["name" => $observationTab['name'], "count" => $observationCount];
                array_push($observationChart, $observationItem);

            }

            $actionChart = [];
            foreach ($actionsTabs as $actionsTab) {

                $actionCount = Action::where('organization_id', $organization->id)->where('status', $actionsTab)->count();
                $actionItem = ["name" => ucfirst($actionsTab), "count" => $actionCount];
                array_push($actionChart, $actionItem);

            }

            return successResponse('Dashboard Fetched Successfully', [
                'actions' => $actions,
                'investigation' => $investigation,
                'inspection' => $inspection,
                'observationChart' => $observationChart,
                'actionChart' => $actionChart,
                'report' => $report,
            ]);

        } catch (\Throwable $th) {
            logger($th);
            return successResponse('Dashboard Fetched Successfully', []);

        }
    }
}
