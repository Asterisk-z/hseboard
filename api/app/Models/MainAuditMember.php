<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainAuditMember extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['member'];

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
