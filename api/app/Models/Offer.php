<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $guarded = [];

    const INVITE = 'invite';
    const REQUEST = 'request';
    const PENDING = 'pending';

    public static function generateTypeMessage($type, $organization_name, $recipient, $email)
    {
        if ($type == self::INVITE) {
            return "$email invites $recipient to join $organization_name";
        }

        if ($type == self::REQUEST) {
            return "$recipient sent a request to join $organization_name";
        }

        return '';

    }
}
