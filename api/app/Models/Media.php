<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['full_url'];
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullUrlAttribute()
    {
        return $this->file_name ? (config('app.url') . '' . config("app.storage_link") . '/' . $this->file_name) : "";
    }
}
