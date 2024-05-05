<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitToWorkMember extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['member'];
    protected $appends = ['is_declared'];

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'member_id');
    }

    public function getIsDeclaredAttribute()
    {
        return $this->declaration == 'yes' ? true : false;
    }
}
