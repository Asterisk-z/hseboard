<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['is_accepted', 'is_sent'];

    public function getIsAcceptedAttribute()
    {
        return $this->accepted_time ? true : false;
    }
    public function getIsSentAttribute()
    {
        return $this->status == 'sent' ? true : false;
    }
}
