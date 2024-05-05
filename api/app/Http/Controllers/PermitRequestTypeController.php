<?php

namespace App\Http\Controllers;

use App\Models\PermitRequestType;
use Exception;
use Illuminate\Http\Request;

class PermitRequestTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $stats = PermitRequestType::where('is_del', 'no')->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Type Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Type Fetched Successfully', []);

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
     * @param  \App\Models\PermitRequestType  $permitRequestType
     * @return \Illuminate\Http\Response
     */
    public function show(PermitRequestType $permitRequestType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PermitRequestType  $permitRequestType
     * @return \Illuminate\Http\Response
     */
    public function edit(PermitRequestType $permitRequestType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PermitRequestType  $permitRequestType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PermitRequestType $permitRequestType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PermitRequestType  $permitRequestType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PermitRequestType $permitRequestType)
    {
        //
    }
}
