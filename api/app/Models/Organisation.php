<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['industry', 'country'];
    // protected $appends = ['user'];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // public function user_obj()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'id');
    // }

    // public function getUserAttribute()
    // {
    //     return User::where('id', $this->user_id)->first();

    // }
    public static function all_users_except_active_user($model)
    {

        $user_ids = OrganisationUser::where('organization_id', $model->id)->pluck('user_id');

        return User::whereIn('id', $user_ids)->where('id', '!=', auth()->user()->id)->get();
    }
    public static function all_users($model)
    {

        $user_ids = OrganisationUser::where('organization_id', $model->id)->pluck('user_id');

        return User::whereIn('id', $user_ids)->get();
    }

    public static function find_user($model, $user_email)
    {
        $user_ids = OrganisationUser::where('organization_id', $model->id)->pluck('user_id');
        return User::whereIn('id', $user_ids)->where('email', $user_email)->first();
    }

    public static function owner($model)
    {
        return User::where('id', $model->id)->first();
    }
}
