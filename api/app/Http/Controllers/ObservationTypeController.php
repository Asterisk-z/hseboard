<?php

namespace App\Http\Controllers;

use App\Models\ObservationType;
use Illuminate\Http\Request;

class ObservationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $observation_types = ObservationType::where('is_active', 'yes')->get(['id', 'name', 'description', 'require_number_affected'])->toArray();
        $converted_observation_types = arrayKeysToCamelCase($observation_types);
        return successResponse('Observation Types Fetched Successfully', $converted_observation_types);
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
     * @param  \App\Models\ObservationType  $observationType
     * @return \Illuminate\Http\Response
     */
    public function show(ObservationType $observationType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ObservationType  $observationType
     * @return \Illuminate\Http\Response
     */
    public function edit(ObservationType $observationType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ObservationType  $observationType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObservationType $observationType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ObservationType  $observationType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObservationType $observationType)
    {
        //
    }
}
