<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $guarded = [];

    const status = [
        'PEN' => 'pending',
        'ACC' => 'accepted',
        'REJ' => 'rejected',
        'ONG' => 'ongoing',
        'CAN' => 'canceled',
        'COM' => 'completed',
    ];
    protected $appends = ['can_accept', 'can_start', 'is_pending', 'is_accepted', 'is_ongoing'];

    protected $with = ['assignee', 'creator', 'priority'];

    public function assignee()
    {
        return $this->hasOne(User::class, 'id', 'assignee_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function priority()
    {
        return $this->hasOne(Priority::class, 'id', 'priority_id');
    }

    public function getCanAcceptAttribute()
    {
        return now();
        return Carbon::create($this->start_datetime) < now() ? true : false;
    }

    public function getCanStartAttribute()
    {
        return Carbon::create($this->start_datetime);
        return Carbon::create($this->start_datetime) >= now() ? true : false;
    }

    public function getIsPendingAttribute()
    {
        return $this->status == 'pending' ? true : false;
    }
    public function getIsAcceptedAttribute()
    {
        return $this->status == 'accepted' ? true : false;
    }
    public function getIsOngoingAttribute()
    {
        return $this->status == 'ongoing' ? true : false;
    }

}
