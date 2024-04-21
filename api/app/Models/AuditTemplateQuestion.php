<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTemplateQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $with = ['questions'];

    public function questions()
    {
        return $this->hasMany(AuditTemplateQuestion::class, 'topic_id', 'id');
    }

    public function response()
    {
        return $this->hasOne(MainAuditQuestionResponse::class, 'audit_question_id', 'id');
    }

    public function group()
    {
        return $this->hasMany(AuditTemplateQuestion::class, 'group_id', 'id');
    }

    public function header()
    {
        return $this->hasMany(AuditTemplateQuestion::class, 'header_id', 'id');
    }

}
