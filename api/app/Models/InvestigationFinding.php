<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InvestigationFinding extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['user', 'media'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }
}
