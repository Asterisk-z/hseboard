<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Observation;
use App\Models\Organisation;
use App\Models\Statistics;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $stats = Statistics::where('is_del', 'no');

            $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Report Fetched Successfully', $converted_stats);

        } catch (Exception $error) {
            logger($error);

            return successResponse('Report Fetched Successfully', []);

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d'],
            'number_of_workers' => ['required', 'integer'],
            'number_of_working_hours_per_day' => ['required', 'integer'],
            'number_of_days_under_observation' => ['required', 'integer'],
            'number_of_injured_or_sick_workers' => ['required', 'integer'],
            'average_number_of_working_days_away_from_work' => ['required', 'integer'],
            'number_of_workers_on_leave' => ['required', 'integer'],
            'average_number_of_days_spent_on_leave' => ['required', 'integer'],
            'average_number_of_overtime_hours_per_day' => ['required', 'integer'],
            'average_number_of_overtime_days' => ['required', 'integer'],
            'number_of_workers_on_overtime' => ['required', 'integer'],
            'hse_meetings_target' => ['required', 'integer'],
            'hse_meetings_actual' => ['required', 'integer'],
            'toolbox_talks_target' => ['required', 'integer'],
            'toolbox_talks_actual' => ['required', 'integer'],
            'hse_inspection_target' => ['required', 'integer'],
            'hse_inspection_actual' => ['required', 'integer'],
            'drills_target' => ['required', 'integer'],
            'drills_actual' => ['required', 'integer'],
            'unsafe_acts_target' => ['required', 'integer'],
            'unsafe_conditions_target' => ['required', 'integer'],
            'days_interval' => ['required', 'integer'],
            'total_man_hours' => ['required', 'integer'],
            'lost_working_hours' => ['required', 'integer'],
            'leave_hours' => ['required', 'integer'],
            'total_overtime' => ['required', 'integer'],
            'actual_man_hour' => ['required', 'integer'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Organization Not Found");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            Statistics::create([
                'user_id' => auth()->user()->id,
                'organization_id' => $organization->id,
                'start_date' => Carbon::create($request->start_date),
                'end_date' => Carbon::create($request->end_date),
                'number_of_workers' => $request->number_of_workers,
                'number_of_working_hours_per_day' => $request->number_of_working_hours_per_day,
                'number_of_days_under_observation' => $request->number_of_days_under_observation,
                'number_of_injured_or_sick_workers' => $request->number_of_injured_or_sick_workers,
                'average_number_of_working_days_away_from_work' => $request->average_number_of_working_days_away_from_work,
                'number_of_workers_on_leave' => $request->number_of_workers_on_leave,
                'average_number_of_days_spent_on_leave' => $request->average_number_of_days_spent_on_leave,
                'average_number_of_overtime_hours_per_day' => $request->average_number_of_overtime_hours_per_day,
                'average_number_of_overtime_days' => $request->average_number_of_overtime_days,
                'number_of_workers_on_overtime' => $request->number_of_workers_on_overtime,
                'hse_meetings_target' => $request->hse_meetings_target,
                'hse_meetings_actual' => $request->hse_meetings_actual,
                'toolbox_talks_target' => $request->toolbox_talks_target,
                'toolbox_talks_actual' => $request->toolbox_talks_actual,
                'hse_inspection_target' => $request->hse_inspection_target,
                'hse_inspection_actual' => $request->hse_inspection_actual,
                'drills_target' => $request->drills_target,
                'drills_actual' => $request->drills_actual,
                'unsafe_acts_target' => $request->unsafe_acts_target,
                'unsafe_conditions_target' => $request->unsafe_conditions_target,
                'days_interval' => $request->days_interval,
                'total_man_hours' => $request->total_man_hours,
                'lost_working_hours' => $request->lost_working_hours,
                'leave_hours' => $request->leave_hours,
                'total_overtime' => $request->total_overtime,
                'actual_man_hour' => $request->actual_man_hour,
            ]);

            logAction(auth()->user()->email, 'You created an HSE report ', 'HSE Report', $request->ip());
            // SENDMAIL

            return successResponse('HSE Report Created Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $data = $request->validate([
            'report_id' => ['required', 'string'],
        ]);

        try {

            if (!$hse_report = Statistics::where('uuid', $data['report_id'])->where('is_del', 'no')->where('user_id', auth()->user()->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find HSE Report");
            }

            $hse_report->is_del = 'yes';
            $hse_report->save();

            logAction(auth()->user()->email, 'You deleted an HSE Report ', 'HSE Report Delete', $request->ip());
            // SENDMAIl

            if ($organization = Organisation::where('id', $hse_report->organization_id)->first()) {
                $organization_owner = Organisation::owner($organization);
                logAction($organization_owner->email, 'Organization HSE Report Delete', 'Delete HSE Report', $request->ip());
                // SENDMAIl
            }

            return successResponse('HSE Report Deleted Successfully', []);

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try {

            $stats = [];

            if (!$statistics = Statistics::where('uuid', request('report_id'))->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "There is no running statistics");
            }

            $target_man_hours = $statistics->number_of_workers * $statistics->number_of_working_hours_per_day * $statistics->number_of_days_under_observation;

            $lost_working_hours = $statistics->number_of_injured_or_sick_workers * $statistics->number_of_working_hours_per_day * $statistics->average_number_of_working_days_away_from_work;

            $leave_hours = $statistics->number_of_workers_on_leave * $statistics->number_of_working_hours_per_day * $statistics->average_number_of_days_spent_on_leave;

            $overtime = $statistics->number_of_workers_on_overtime * $statistics->average_number_of_overtime_hours_per_day * $statistics->average_number_of_overtime_days;

            $actual_man_hours = $target_man_hours - $lost_working_hours - $leave_hours + $overtime;

            if ($actual_man_hours > 0) {

                $percentage_of_hours_on_overtime = $overtime * 100 / $actual_man_hours;
            } else {
                $percentage_of_hours_on_overtime = 0;
            }

            $unsafe_acts_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 1)->get()->count();

            $unsafe_conditions_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 2)->get()->count();

            $near_miss_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 3)->get()->count();

            $first_aid_case_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 4)->get()->sum('number_of_workers_affected');

            $restricted_work_case_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 5)->get()->sum('number_of_workers_affected');

            $road_traffic_accident_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 6)->get()->count();

            $medical_case_treatment_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 7)->get()->sum('number_of_workers_affected');

            $lost_workday_case_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 8)->get()->sum('number_of_workers_affected');

            $dangerous_occurance_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 9)->get()->count();

            $permanent_partial_disability_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 10)->get()->sum('number_of_workers_affected');

            $permamnent_total_disabilities_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 11)->get()->sum('number_of_workers_affected');

            $fatality_observations = Observation::whereBetween('created_at', [$statistics->start_date, $statistics->end_date])->where('organization_id', $statistics->organization_id)->where('observation_type_id', 12)->get()->sum('number_of_workers_affected');

            $stats['main_report'] = $statistics;
            $stats['stats'] = [];
            $stats['stats']['target_man_hours'] = $target_man_hours;
            $stats['stats']['lost_working_hours'] = $lost_working_hours;
            $stats['stats']['leave_hours'] = $leave_hours;
            $stats['stats']['overtime'] = $overtime;
            $stats['stats']['actual_man_hours'] = $actual_man_hours;
            $stats['stats']['percentage_of_hours_on_overtime'] = number_format($percentage_of_hours_on_overtime, 2);
            $stats['stats']['unsafe_acts_observations'] = $unsafe_acts_observations;
            $stats['stats']['unsafe_conditions_observations'] = $unsafe_conditions_observations;
            $stats['stats']['near_miss_observations'] = $near_miss_observations;
            $stats['stats']['first_aid_case_observations'] = $first_aid_case_observations;
            $stats['stats']['restricted_work_case_observations'] = $restricted_work_case_observations;
            $stats['stats']['road_traffic_accident_observations'] = $road_traffic_accident_observations;
            $stats['stats']['medical_case_treatment_observations'] = $medical_case_treatment_observations;
            $stats['stats']['lost_workday_case_observations'] = $lost_workday_case_observations;
            $stats['stats']['dangerous_occurance_observations'] = $dangerous_occurance_observations;
            $stats['stats']['permanent_partial_disability_observations'] = $permanent_partial_disability_observations;
            $stats['stats']['permamnent_total_disabilities_observations'] = $permamnent_total_disabilities_observations;
            $stats['stats']['fatality_observations'] = $fatality_observations;
            $stats['stats']['lost_time_injury'] = $lost_workday_case_observations + $permanent_partial_disability_observations + $permamnent_total_disabilities_observations + $fatality_observations;
            $stats['stats']['lost_time_injury_frequency'] = ($lost_workday_case_observations + $permanent_partial_disability_observations + $permamnent_total_disabilities_observations + $fatality_observations) * 1000000 / $actual_man_hours;
            $stats['stats']['total_recorded_cases'] = $restricted_work_case_observations + $medical_case_treatment_observations + $lost_workday_case_observations + $permanent_partial_disability_observations + $permamnent_total_disabilities_observations + $fatality_observations;
            $stats['stats']['total_recorded_cases_frequency'] = round((($restricted_work_case_observations + $medical_case_treatment_observations + $lost_workday_case_observations + $permanent_partial_disability_observations + $permamnent_total_disabilities_observations + $fatality_observations) * 1000000 / $actual_man_hours), 2);

            $stats['stats']['year'] = Carbon::parse($statistics->created_at)->format('Y');

            $stats['stats']['month'] = Carbon::parse($statistics->created_at)->format('M');

            $stats['stats']['start_onsite'] = 32323;

            return successResponse('statistics Fetched Successfully', $stats);

        } catch (Exception $error) {

            return successResponse('statistics Fetched Successfully', []);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return \Illuminate\Http\Response
     */
    public function edit(Statistics $statistics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Statistics  $statistics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Statistics $statistics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return \Illuminate\Http\Response
     */
    public function destroy(Statistics $statistics)
    {
        //
    }
}
