<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationInterview extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['user', 'invitee'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function invitee()
    {
        return $this->hasOne(User::class, 'id', 'invitee_id');
    }

}
