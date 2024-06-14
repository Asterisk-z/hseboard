<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Observation extends Model
{
    use HasFactory;

    protected $guarded = [];

    const status = [
        'PEI' => 'pending investigation',
        'BEI' => 'being investigated',
        'REI' => 'reinvestigating',
        'DOI' => 'done investigation',
    ];

    protected $with = ['observer', 'observation_type', 'media'];
    protected $appends = ['is_pending_investigation', 'is_being_investigated', 'is_done_investigation', 'is_reinvestigating'];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function observer()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function observation_type()
    {
        return $this->hasOne(ObservationType::class, 'id', 'observation_type_id');
    }

    public function getIsPendingInvestigationAttribute()
    {
        return $this->status == self::status['PEI'] ? true : false;
    }

    public function getIsBeingInvestigatedAttribute()
    {
        return $this->status == self::status['BEI'] ? true : false;
    }

    public function getIsReinvestigatingAttribute()
    {
        return $this->status == self::status['REI'] ? true : false;
    }

    public function getIsDoneInvestigationAttribute()
    {
        return $this->status == self::status['DOI'] ? true : false;
    }
}
