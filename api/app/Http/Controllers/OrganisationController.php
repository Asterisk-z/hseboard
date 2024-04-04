<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Organisation;
use Exception;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $organizations = Organisation::orderBy('created_at', 'DESC')
            ->get()->toArray();

        $converted_organizations = arrayKeysToCamelCase($organizations);
        return successResponse('Organizations Fetched Successfully', $converted_organizations);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function show($organization)
    {
        $organizations = Organisation::where('uuid', $organization)->orderBy('created_at', 'DESC')
            ->get()->toArray();

        $converted_organizations = arrayKeysToCamelCase($organizations);
        return successResponse('Organizations Fetched Successfully', $converted_organizations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request)
    {
        $data = $request->validate([
            'organization_id' => ['required'],
            'user_id' => ['required'],
        ]);

        try {

            $organization = checkOrganizationOwner($request->organization_id);

            $user = findUser($request->user_id);

            // if ($recipient = Organisation::find_user($organization, $recipientUserEmail)) {
            //     return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is already part of organization");
            // }

            logger('$organization');
            logger($organization);
            logger('$user');
            logger($user);

            return successResponse('Organizations Fetched Successfully', []);

        } catch (Exception $e) {

            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function edit(Organisation $organisation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organisation $organisation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation)
    {
        //
    }
}
