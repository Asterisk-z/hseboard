<?php

namespace App\Http\Controllers;

use App\Models\InspectionType;
use Exception;
use Illuminate\Http\Request;

class InspectionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = InspectionType::where('is_del', 'no')->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Inspection Type Fetched Successfully', $converted_stats);

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
     * @param  \App\Models\InspectionType  $inspectionType
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionType $inspectionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InspectionType  $inspectionType
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionType $inspectionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InspectionType  $inspectionType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InspectionType $inspectionType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InspectionType  $inspectionType
     * @return \Illuminate\Http\Response
     */
    public function destroy(InspectionType $inspectionType)
    {
        //
    }
}
