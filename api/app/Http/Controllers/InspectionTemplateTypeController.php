<?php

namespace App\Http\Controllers;

use App\Models\InspectionTemplateType;
use Exception;
use Illuminate\Http\Request;

class InspectionTemplateTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = InspectionTemplateType::where('is_del', 'no')->orderBy('created_at', 'desc')->first();

            return successResponse('Inspection Type Fetched Successfully', $stats);

        } catch (Exception $error) {

            return successResponse('Inspection Type Fetched Successfully', []);

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
     * @param  \App\Models\InspectionTemplateType  $inspectionTemplateType
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionTemplateType $inspectionTemplateType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InspectionTemplateType  $inspectionTemplateType
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionTemplateType $inspectionTemplateType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InspectionTemplateType  $inspectionTemplateType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InspectionTemplateType $inspectionTemplateType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InspectionTemplateType  $inspectionTemplateType
     * @return \Illuminate\Http\Response
     */
    public function destroy(InspectionTemplateType $inspectionTemplateType)
    {
        //
    }
}
