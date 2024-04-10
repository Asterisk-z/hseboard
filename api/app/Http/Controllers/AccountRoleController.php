<?php

namespace App\Http\Controllers;

use App\Models\AccountRole;
use Illuminate\Http\Request;

class AccountRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $account_roles = AccountRole::where('is_active', 'yes')->get(['id', 'name', 'description'])->toArray();
        $converted_account_roles = arrayKeysToCamelCase($account_roles);
        return successResponse('Account Roles Fetched Successfully', $converted_account_roles);
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
     * @param  \App\Models\AccountRole  $accountRole
     * @return \Illuminate\Http\Response
     */
    public function show(AccountRole $accountRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountRole  $accountRole
     * @return \Illuminate\Http\Response
     */
    public function edit(AccountRole $accountRole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountRole  $accountRole
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccountRole $accountRole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountRole  $accountRole
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountRole $accountRole)
    {
        //
    }
}
