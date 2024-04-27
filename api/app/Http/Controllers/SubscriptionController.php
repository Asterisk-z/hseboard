<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\Currency;
use App\Models\Feature;
use App\Models\Organisation;
use App\Models\OrganizationFeature;
use App\Models\SubscribedFeature;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $stats = Subscription::query();

            $organization = Organisation::where('uuid', request('organization_id'))->first();

            $stats = $stats->where(function ($query) use ($organization) {
                $query->orWhere('user_id', auth()->user()->id);
                if ($organization) {
                    $query->orWhere('organization_id', $organization->id);
                }
            })->orderBy('created_at', 'desc')->get();

            $converted_stats = arrayKeysToCamelCase($stats);

            return successResponse('Transaction Fetched Successfully', $converted_stats);

        } catch (Exception $error) {

            return successResponse('Transaction Fetched Successfully', []);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function plans()
    {

        $plans = SubscriptionPlan::orderBy('no_of_months', 'DESC')->get()->toArray();
        $converted_plans = arrayKeysToCamelCase($plans);
        return successResponse('plans Fetched Successfully', $converted_plans);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function initiate(Request $request)
    {
        //
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
            'plan_id' => ['required', 'integer'],
            'currency_id' => ['required', 'integer'],
            'amount' => ['required', 'decimal:0'],
            'features' => ['required', 'array'],
            'features.*' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', $data['organization_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            if ($organization->user_id != auth()->user()->id) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            }

            if (!$currency = Currency::where('id', $data['currency_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Currency");
            }

            if (!$plan = SubscriptionPlan::where('id', $data['plan_id'])->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Plan");
            }

            DB::beginTransaction();

            // DB::transaction(function () use ($features, $organization, $currency, $plan) {

            $features = request('features');

            $transaction = Subscription::create([
                'amount' => request('amount'),
                'organization_id' => $organization->id,
                'user_id' => auth()->user()->id,
                'currency_id' => $currency->id,
            ]);

            $cost = 0;

            foreach ($features as $feature) {
                logger($feature);
                if (!$default_feature = Feature::where('uuid', $feature)->first()) {
                    DB::rollBack();
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Feature");
                }

                $organization_feature = OrganizationFeature::firstOrCreate([
                    'organization_id' => $organization->id,
                    'feature_id' => $default_feature->id,
                ], [
                    'organization_id' => $organization->id,
                    'feature_id' => $default_feature->id,
                    'cost' => $default_feature->cost,
                ]);

                if ($organization_feature->end_date && $organization_feature->end_date > now()) {
                    DB::rollBack();
                    return errorResponse(ResponseStatusCodes::BAD_REQUEST, "One of the selected features is running");
                }

                $cost = $cost + floatval($organization_feature->cost);

                SubscribedFeature::create([
                    "organization_feature_id" => $organization_feature->id,
                    "transaction_id" => $transaction->id,
                    "currency_id" => $currency->id,
                    "cost" => $organization_feature->cost,
                    "plan_id" => $plan->id,
                ]);

            }
            $transaction->amount = $cost;
            $transaction->save();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('app.flutter_wave'),
            ])->post("https://api.flutterwave.com/v3/payments", [
                'tx_ref' => $transaction->uuid, //U
                "amount" => $cost,
                "currency" => $currency->code,
                "payment_options" => "card,ussd,banktransfer",
                "redirect_url" => route('verify_payment', ['organization_id' => $organization->uuid]),
                // "meta" => [
                //     "consumer_id" => '34',
                //     "consumer_mac" => 'frfrfrfr34',
                // ],
                "customer" => [
                    "email" => auth()->user()->email,
                    "phonenumber" => auth()->user()->phone,
                    "name" => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ],
                "customizations" => [
                    "title" => "HSE board",
                    "logo" => "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png",
                ],
            ]);

            $result = $response->json();

            logger($result);

            if ($result['status'] == 'error') {

                DB::rollBack();
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Fail to Create Payment Link");

            }

            DB::commit();
            // });

            // logAction(auth()->user()->email, 'You started an Audit ', 'Start Audit', $request->ip());
            if ($transaction) {

                return successResponse('Billing initiated Successfully', $result);

            }

            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        //

        logger(request()->all());
        logger(request('organization_id'));
        $data = $request->validate([
            'tx_ref' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);

        try {

            if (!$organization = Organisation::where('uuid', request('organization_id'))->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Organization");
            }

            // if ($organization->user_id != auth()->user()->id) {
            //     return errorResponse(ResponseStatusCodes::BAD_REQUEST, "User not authorized to perform action for organization");
            // }

            if (!$subscription = Subscription::where('uuid', $data['tx_ref'])->where('status', 'pending')->first()) {
                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Subscription");
            }

            DB::beginTransaction();

            $features = $subscription->features;

            if (request('status') == 'successful') {

                if (count($features) > 0) {
                    foreach ($features as $feature) {

                        if (!$organization_feature = OrganizationFeature::where('id', $feature->organization_feature_id)->where('organization_id', $organization->id)->first()) {
                            DB::rollBack();
                            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Unable to find Feature");
                        }

                        $organization_feature->start_date = now();
                        $organization_feature->end_date = now()->addMonths($feature->plan->no_of_months);
                        $organization_feature->is_running = 'yes';
                        $organization_feature->save();

                    }
                    $subscription->status = 'success';
                    $subscription->save();
                }

            } else {

                $subscription->status = 'failed';
                $subscription->save();
            }
            DB::commit();
            // });

            // logAction(auth()->user()->email, 'You started an Audit ', 'Start Audit', $request->ip());

            return redirect(config('app.web_url') . '/billing');

        } catch (Exception $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
