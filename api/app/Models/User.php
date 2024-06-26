<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['organizations', 'privilege', 'full_name', 'is_email_verified'];
    protected $with = ['accountRole', 'media', 'activeOrg'];

    public function media(): MorphOne
    {
        return $this->morphOne(Media::class, 'model');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getOrganizationsAttribute()
    {
        $org_ids = OrganisationUser::where('user_id', $this->id)->pluck('organization_id');
        return Organisation::whereIn('id', $org_ids)->get();
    }

    public function getPrivilegeAttribute()
    {

        return [];
    }

    public function getFullNameAttribute()
    {

        return $this->lastName . ' ' . $this->firstName;
    }

    public function getIsEmailVerifiedAttribute()
    {

        if (!ActionToken::where('email', $this->email)->where('status', ActionToken::status['COM'])->where('type', ActionToken::types['EV'])->first()) {
            return false;
        }
        return true;
    }
    public function accountRole()
    {
        return $this->hasOne(AccountRole::class, 'id', 'account_role_id');
    }

    public function activeOrg()
    {
        return $this->hasOne(Organisation::class, 'uuid', 'active_organization');
    }

}
