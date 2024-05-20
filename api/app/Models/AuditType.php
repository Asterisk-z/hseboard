<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditType extends Model
{
    use HasFactory;
    protected $appends = ['link'];

    public function getLinkAttribute()
    {
        return $this->sample ? (config('app.url') . '/' . $this->sample) : "";
        // return $this->sample ? (config('app.url') . '' . config("app.storage_link") . '/' . $this->sample) : "";
    }
}
