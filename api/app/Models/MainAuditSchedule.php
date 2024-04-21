<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainAuditSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['is_accepted'];

    public function getIsAcceptedAttribute()
    {
        return $this->accepted_time ? true : false;
    }
}
