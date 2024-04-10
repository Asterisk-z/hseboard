<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountRole extends Model
{
    use HasFactory;

    protected $guarded = [];

    const role = [
        'member' => 'member',
        'supervisor' => 'supervisor',
        'manager' => 'manager',
    ];

    const roles = [
        'MEM' => 'member',
        'SUB' => 'supervisor',
        'MAN' => 'manager',
    ];

}
