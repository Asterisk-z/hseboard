<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditType extends Model
{
    use HasFactory;
    protected $appends = ['link', 'code'];

    public function getLinkAttribute()
    {
        return $this->sample ? (config('app.url') . '/' . $this->sample) : "";
        // return $this->sample ? (config('app.url') . '' . config("app.storage_link") . '/' . $this->sample) : "";
    }

    public function getCodeAttribute()
    {
        $name = '';
        if ($this->name == 'hse_management_system_audit') {
            $name = 'HSE';
        }
        if ($this->name == 'environmental_management_system') {
            $name = 'ENVIRONMENT';
        }
        if ($this->name == 'quality_management_audit') {
            $name = 'QUALITY';
        }

        return $name;

    }

}
