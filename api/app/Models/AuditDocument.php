<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AuditDocument extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['user', 'media'];
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function media(): MorphOne
    {
        return $this->morphOne(Media::class, 'model');
    }

}
