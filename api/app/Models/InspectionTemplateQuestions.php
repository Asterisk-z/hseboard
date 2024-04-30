<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionTemplateQuestions extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(InspectionTemplateQuestions::class, 'topic_id', 'id');
    }

    public function response()
    {
        return $this->hasOne(InspectionQuestionResponse::class, 'inspection_question_id', 'id');
    }

    public function group()
    {
        return $this->hasMany(InspectionTemplateQuestions::class, 'group_id', 'id');
    }

    public function header()
    {
        return $this->hasMany(InspectionTemplateQuestions::class, 'header_id', 'id');
    }
}
