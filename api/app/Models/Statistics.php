<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['organization', 'user'];

    public function organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'organization_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
