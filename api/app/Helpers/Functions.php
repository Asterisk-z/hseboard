<?php

use App\Helpers\ResponseStatusCodes;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\JWTGuard;

if (!function_exists('arrayKeysToCamelCase')) {

    function arrayKeysToCamelCase($array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key = Str::camel($key);
            if (is_array($value)) {
                $value = arrayKeysToCamelCase($value);
            }
            $result[$key] = $value;
        }
        return $result;

    }
}

if (!function_exists('loginAuth')) {
    /**
     * The actual auth service provider
     */
    function loginAuth(): JWTGuard
    {
        /**@var JWTGuard */

        $auth = auth('jwt');

        $auth->Manager()->getJWTProvider()->setSecret(config('jwt.secret'));

        return $auth;
    }
}

if (!function_exists('passwordReset')) {
    /**
     * The actual auth service provider
     */
    function passwordReset(): JWTGuard
    {
        /**@var JWTGuard */
        $auth = auth('jwt');

        $auth->Manager()->getJWTProvider()->setSecret(config('jwt.reset_secret'));

        return $auth;
    }
}

if (!function_exists('successResponse')) {
    function successResponse($message = "Successful.", $data = [])
    {
        return response()->json([
            "status" => true,
            "statusCode" => ResponseStatusCodes::OK,
            "message" => $message,
            "data" => $data,
        ], Response::HTTP_OK);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(int $statusCode = ResponseStatusCodes::BAD_REQUEST, string $message = "An error occurred.", $data = [], $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        return response()->json([
            "status" => false,
            "statusCode" => $statusCode,
            "message" => $message,
            "data" => $data,
        ], $code);
    }
}

if (!function_exists('logAction')) {
    function logAction($user, $action, $description, $ipAddress = null)
    {
        Audit::create([
            'user_id' => $user->id,
            'action_performed' => $action,
            'description' => $description,
            'ip_address' => $ipAddress,
        ]);
    }
}

if (!function_exists('generateReference')) {
    function generateReference()
    {
        $dateTime = now();
        $uniqueId = Str::random(8); // Using Laravel's Str::random for simplicity
        $paymentReference = $dateTime->format('YmdHis') . $uniqueId;

        return $paymentReference;
    }
}

if (!function_exists('getAdminUsers')) {
    function getAdminUsers()
    {
        return User::where('is_admin', 'yes')->get();
    }
}

if (!function_exists('formatDate')) {
    function formatDate($inputDate)
    {
        if (!$inputDate) {
            return '';
        }

        try {
            $dateTime = new DateTime($inputDate);
            return $dateTime->format('M. j, Y');
        } catch (\Throwable $th) {
            return $inputDate;
        }
    }
}
