<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['features', 'currency', 'organization', 'user'];

    public function features()
    {
        return $this->hasMany(SubscribedFeature::class, 'transaction_id', 'id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'organization_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
