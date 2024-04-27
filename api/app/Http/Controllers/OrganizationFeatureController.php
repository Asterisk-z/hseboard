<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Organisation;
use App\Models\OrganizationFeature;
use Illuminate\Http\Request;

class OrganizationFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!$organization = Organisation::where('uuid', request('organization_id'))->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
        }

        $features = OrganizationFeature::where('organization_id', $organization->id)->get()->toArray();
        $converted_features = arrayKeysToCamelCase($features);
        return successResponse('Organization Features Fetched Successfully', $converted_features);

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
     * @param  \App\Models\OrganizationFeature  $organizationFeature
     * @return \Illuminate\Http\Response
     */
    public function show(OrganizationFeature $organizationFeature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrganizationFeature  $organizationFeature
     * @return \Illuminate\Http\Response
     */
    public function edit(OrganizationFeature $organizationFeature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrganizationFeature  $organizationFeature
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrganizationFeature $organizationFeature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrganizationFeature  $organizationFeature
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrganizationFeature $organizationFeature)
    {
        //
    }
}
