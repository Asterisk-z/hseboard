<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionTemplate extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['questions'];

    public function questions()
    {
        return $this->hasMany(InspectionTemplateQuestions::class, 'inspection_template_id', 'id')->where('topic_id', '!=', null);
    }
}
