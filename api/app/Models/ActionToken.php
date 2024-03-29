<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const types = [
        'EV' => 'Email Verification',
        'PR' => 'Password Reset',
    ];

    const status = [
        'PEN' => 'Pending',
        'COM' => 'Completed',
    ];

}
