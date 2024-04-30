<?php

namespace App\Http\Controllers;

use App\Models\InspectionTemplate;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class InspectionTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_type()
    {
        try {

            $stats = InspectionTemplate::where('is_del', 'no')->where('inspection_template_type_id', request('type_id'));

            $organization = Organisation::where('uuid', request('organization_id'))->first();

            $stats = $stats->where(function ($query) use ($organization) {

                $query->orWhere('organization_id', $organization->id);
                $query->orWhere('organization_id', null);

            });
            $stats = $stats->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Inspection Template Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Inspection Template Fetched Successfully', []);

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InspectionTemplate  $inspectionTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionTemplate $inspectionTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InspectionTemplate  $inspectionTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionTemplate $inspectionTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InspectionTemplate  $inspectionTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InspectionTemplate $inspectionTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InspectionTemplate  $inspectionTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(InspectionTemplate $inspectionTemplate)
    {
        //
    }
}
