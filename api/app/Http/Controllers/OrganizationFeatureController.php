<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Organisation;
use App\Models\OrganizationFeature;

class OrganizationFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!$organization = Organisation::where('uuid', auth()->user()->active_organization)->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
        }

        $features = OrganizationFeature::where('organization_id', $organization->id)->get()->toArray();
        $converted_features = arrayKeysToCamelCase($features);
        return successResponse('Organization Features Fetched Successfully', $converted_features);

    }

}
