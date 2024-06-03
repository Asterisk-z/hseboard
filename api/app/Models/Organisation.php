<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Organisation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['industry', 'country', 'media'];
    protected $appends = ['privilege'];

    public function media(): MorphOne
    {
        return $this->morphOne(Media::class, 'model');
    }
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

    public function getPrivilegeAttribute()
    {
        // $sub

        return OrganizationFeature::where('organization_id', $this->id)->where('end_date', '>', now())->get();

    }
    public static function all_users_except_active_user($model)
    {
        $id = $model ? $model->id : '';

        $user_ids = OrganisationUser::where('organization_id', $id)->pluck('user_id');

        return User::whereIn('id', $user_ids)->where('id', '!=', auth()->user()->id)->get();
    }
    public static function all_users($model)
    {

        $id = $model ? $model->id : '';

        $user_ids = OrganisationUser::where('organization_id', $id)->pluck('user_id');

        return User::whereIn('id', $user_ids)->get();
    }

    public static function find_user($model, $user_email)
    {

        $id = $model ? $model->id : '';

        $user_ids = OrganisationUser::where('organization_id', $id)->pluck('user_id');
        return User::whereIn('id', $user_ids)->where('email', $user_email)->first();
    }

    public static function owner($model)
    {
        return User::where('id', $model->user_id)->first();
    }

    public function getToken()
    {
        return $this->token ? $this->token : $this->createToken();
    }

    public function refreshToken()
    {
        return $this->createToken();
    }

    private function createToken(): string
    {
        preg_match_all('/(?<=\s|^)\w/iu', $this->name, $matches);
        $words = implode('', $matches[0]);
        $result = mb_strtoupper($words);
        $number = strtoupper(generateRandomString(10));

        $this->token = 'HSE' . $result . '' . $number;
        if (Organisation::where('token', $this->token)->exists()) {
            $this->createToken();
        }

        return $this->token;

    }
}
