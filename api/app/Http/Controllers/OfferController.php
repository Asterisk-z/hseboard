<?php

namespace App\Http\Controllers;

use App\Helpers\MailContents;
use App\Helpers\ResponseStatusCodes;
use App\Http\Requests\OfferCreateRequest;
use App\Models\Offer;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
use App\Notifications\InfoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = Offer::where('action_user_email', auth()->user()->email)
            ->orWhere('recipient_email', auth()->user()->email)
            ->get()->toArray();

        $converted_offers = arrayKeysToCamelCase($offers);
        return successResponse('Offers Fetched Successfully', $converted_offers);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OfferCreateRequest $request)
    {
        $data = $request->validated();

        if (!$organization = Organisation::where('uuid', $request->organization_id)->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
        }

        $actionUserEmail = auth()->user()->email;
        $recipientUserEmail = $data['type'] == Offer::INVITE ? $data['recipient_email'] : Organisation::owner($organization)->email;
        $typeMessage = Offer::generateTypeMessage($data['type'], $organization->name, $recipientUserEmail, $actionUserEmail);

        if ($data['type'] == Offer::INVITE) {

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if ($recipient = Organisation::find_user($organization, $recipientUserEmail)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is already part of organization");
            }

            if ($previousRequest = Offer::where('organization_uuid', $organization->uuid)->where('recipient_email', $recipientUserEmail)->where('status', Offer::PENDING)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invite Request Awaiting User Action");
            }
        }

        if ($data['type'] == Offer::REQUEST) {

            if ($recipient = Organisation::find_user($organization, $actionUserEmail)) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is already part of organization");
            }

            if ($previousRequest = Offer::where('organization_uuid', $organization->uuid)->where('action_user_email', $actionUserEmail)->where('status', Offer::PENDING)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Request pending with organization");
            }

            if ($previousRequest = Offer::where('organization_uuid', $organization->uuid)->where('recipient_email', $actionUserEmail)->where('status', Offer::PENDING)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User Has a pending invite from Organization");
            }
        }

        $offer = Offer::create([
            'type' => $data['type'],
            'typeMessage' => $typeMessage,
            'organization_uuid' => $organization->uuid,
            'action_user_email' => $actionUserEmail,
            'recipient_email' => $recipientUserEmail,
            'message' => $data['message'],
        ]);

        if ($data['type'] == Offer::INVITE) {

            $action = null;

            if (!$checkRecipientEmail = User::where('email', $recipientUserEmail)->first()) {

                $action = createActionToken($recipientUserEmail, "IR");
            }

            logAction($recipientUserEmail, 'User Receive an invitation to organization', 'Action Invite', $request->ip());
            logAction($actionUserEmail, 'User Sent an invitation ', 'Action Invite', $request->ip());
            Notification::route('mail', $recipientUserEmail)->notify(new InfoNotification(MailContents::invitationMail($organization->name, $recipientUserEmail, $action), MailContents::invitationSubject()));

            return successResponse('Invite Offer Sent Successfully', $offer);

        }

        if ($data['type'] == Offer::REQUEST) {

            logAction($recipientUserEmail, 'Organization Receive a request from a user', 'Action Request', $request->ip());
            logAction(auth()->user()->email, 'User Sent a Request ', 'Action Request', $request->ip());
            Notification::route('mail', $recipientUserEmail)->notify(new InfoNotification(MailContents::invitationMail($organization->name, $recipientUserEmail), MailContents::invitationSubject()));

            return successResponse('Request Offer Sent Successfully', $offer);

        }

        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invalid Offer Type");

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function action(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "offer_id" => "required",
            "status" => "required|in:accept,decline",
        ]);

        if (!$offer = Offer::where('uuid', $request->offer_id)->where('actioned_at', null)->first()) {
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Offer Request");
        }

        if ($offer->type == Offer::REQUEST) {

            return $this->actionOfferRequest($offer, $request->status);

        }

        if ($offer->type == Offer::INVITE) {

            return $this->actionOfferInvite($offer, $request->status);

        }

        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Invalid Offer");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function show(Offer $offer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function edit(Offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        //
    }

    protected function actionOfferRequest($offer, $status)
    {

        if ($status == 'decline') {
            $offer->status = "rejected";
            $offer->actioned_at = now();
            $offer->save();

            logAction($offer->action_user_email, 'Organization Rejected your request to join them', 'Action Request Declined', request()->ip());
            // Notification::route('mail', $offer->action_user_email)->notify(new InfoNotification(MailContents::invitationRejectMail($organization->name, $recipientUserEmail), MailContents::invitationRejectSubject()));

            return successResponse('Request Offer Rejected Successfully', $offer);

        }

        if ($status == 'accept') {
            $offer->status = "accepted";
            $offer->actioned_at = now();
            $offer->save();

            $user = User::where('email', $offer->action_user_email)->first();
            $organization = Organisation::where('uuid', $offer->organization_uuid)->first();

            if (OrganisationUser::where('user_id', $user->id)->where('organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is already in organization");
            }

            OrganisationUser::create([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);

            logAction($offer->action_user_email, 'Organization Accepted your request to join them', 'Action Request Accepted', request()->ip());
            // Notification::route('mail', $offer->action_user_email)->notify(new InfoNotification(MailContents::invitationRejectMail($organization->name, $recipientUserEmail), MailContents::invitationRejectSubject()));

            return successResponse('Request Offer Accepted Successfully', $offer);

        }

        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to complete Action Request");

    }

    protected function actionOfferInvite($offer, $status)
    {

        if ($status == 'decline') {
            $offer->status = "rejected";
            $offer->actioned_at = now();
            $offer->save();

            logAction($offer->recipient, 'Organization Rejected your request to join them', 'Action Invite Declined', request()->ip());
            // Notification::route('mail', $offer->action_user_email)->notify(new InfoNotification(MailContents::invitationRejectMail($organization->name, $recipientUserEmail), MailContents::invitationRejectSubject()));

            return successResponse('Invited Offer Rejected Successfully', $offer);

        }

        if ($status == 'accept') {
            $offer->status = "accepted";
            $offer->actioned_at = now();
            $offer->save();

            $user = User::where('email', $offer->recipient_email)->first();
            $organization = Organisation::where('uuid', $offer->organization_uuid)->first();

            if (OrganisationUser::where('user_id', $user->id)->where('organization_id', $organization->id)->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User is already in organization");
            }

            OrganisationUser::create([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);

            logAction($offer->recipient_email, 'User Accepted invite to join organization', 'Action Invite Accepted', request()->ip());
            // Notification::route('mail', $offer->action_user_email)->notify(new InfoNotification(MailContents::invitationRejectMail($organization->name, $recipientUserEmail), MailContents::invitationRejectSubject()));

            return successResponse('Invite Offer Accepted Successfully', $offer);

        }

        return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to complete Action Request");

    }
}
