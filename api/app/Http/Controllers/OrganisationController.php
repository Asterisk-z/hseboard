<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
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
            ->first()->toArray();

        $converted_organizations = arrayKeysToCamelCase($organizations);
        return successResponse('Organizations Fetched Successfully', $converted_organizations);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function show_token($org_token)
    {
        $organizations = Organisation::where('token', $org_token)->where('uuid', '!=', request('uuid'))->first();
        return successResponse('Organizations Fetched Successfully', $organizations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request)
    {
        $request->validate([
            'organization_id' => ['required'],
            'user_id' => ['required'],
        ]);

        try {

            if (!$user = User::where('uuid', $request->user_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find User");
            }

            if (!$organization = Organisation::where('uuid', $request->organization_id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id || $organization->user_id == $user->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if (!$relation = OrganisationUser::where('user_id', $user->id)->where('organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is not in organization");
            }

            $relation->delete();

            logAction($user->email, 'Your Account was removed from an organization ', 'Remove user', $request->ip());
            logAction(auth()->user()->email, 'You removed a user from your organization ', 'Remove user', $request->ip());

            return successResponse('User Removed Successfully', []);

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
