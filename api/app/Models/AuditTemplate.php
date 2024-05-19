<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['questions', 'audit_type'];

    public function questions()
    {
        return $this->hasMany(AuditTemplateQuestion::class, 'audit_template_id', 'id')->where('topic_id', '!=', null);
    }

    public function audit_type()
    {
        return $this->hasOne(AuditType::class, 'id', 'audit_type_id');
    }

}
