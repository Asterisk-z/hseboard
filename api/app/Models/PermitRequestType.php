<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitRequestType extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['is_confined_space'];

    const CONFINED_SPACE = 3;

    public function getIsConfinedSpaceAttribute()
    {
        return $this->id == PermitRequestType::CONFINED_SPACE;
    }

}
