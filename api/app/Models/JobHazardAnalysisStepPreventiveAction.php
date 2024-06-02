<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobHazardAnalysisStepPreventiveAction extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['codeText'];

    public function getCodeTextAttribute()
    {
        return "{$this->code}{$this->id}";
    }
}
