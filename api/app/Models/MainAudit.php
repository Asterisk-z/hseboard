<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainAudit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['user', 'recipient_organization', 'organization', 'audit_template', 'audit_type', 'all_auditors', 'all_representatives', 'support_auditor', 'lead_auditor', 'representatives', 'lead_representative', 'findings', 'actions', 'documents', 'schedule'];

    protected $appends = ['questions'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'organization_id');
    }

    public function recipient_organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'recipient_organization_id');
    }

    public function audit_template()
    {
        return $this->hasOne(AuditTemplate::class, 'id', 'audit_template_id');
    }

    public function audit_type()
    {
        return $this->hasOne(AuditType::class, 'id', 'audit_type_id');
    }

    public function all_auditors()
    {
        return $this->hasMany(MainAuditMember::class, 'audit_id', 'id')->where(function ($query) {

            $query->orWhere('position', 'SUPPORT_AUDITOR');
            $query->orWhere('position', 'LEAD_AUDITOR');

        });
    }
    public function all_representatives()
    {
        return $this->hasMany(MainAuditMember::class, 'audit_id', 'id')->where(function ($query) {

            $query->orWhere('position', 'REPRESENTATIVE');
            $query->orWhere('position', 'LEAD_REPRESENTATIVE');

        });
    }
    public function support_auditor()
    {
        return $this->hasOne(MainAuditMember::class, 'audit_id', 'id')->where('position', 'SUPPORT_AUDITOR');
    }
    public function lead_auditor()
    {
        return $this->hasOne(MainAuditMember::class, 'audit_id', 'id')->where('position', 'LEAD_AUDITOR');
    }
    public function representatives()
    {
        return $this->hasMany(MainAuditMember::class, 'audit_id', 'id')->where('position', 'REPRESENTATIVE');
    }
    public function lead_representative()
    {
        return $this->hasOne(MainAuditMember::class, 'audit_id', 'id')->where('position', 'LEAD_REPRESENTATIVE');
    }

    public function documents()
    {
        return $this->hasMany(MainAuditDocument::class, 'audit_id', 'id')->where('is_del', 'no');
    }

    public function schedule()
    {
        return $this->hasOne(MainAuditSchedule::class, 'audit_id', 'id');
    }

    public function getQuestionsAttribute()
    {
        $template = AuditTemplate::where('id', $this->audit_template_id)->where('audit_type_id', $this->audit_type_id)->first();
        $topic_ids = AuditTemplateQuestion::where('audit_template_id', $template->id)->where('topic_id', '!=', null)->pluck('topic_id');
        $audit_id = $this->id;
        $questions = AuditTemplateQuestion::whereIn('id', $topic_ids)->with([
            'questions' => function (Builder $query) use ($audit_id) {
                // $query->where('title', 'like', '%code%');
                // audit_id
                $query->with(['response' => function (Builder $sub_query) use ($audit_id) {
                    $sub_query->where('audit_id', $audit_id);
                }]);
                // $query->leftJoin('main_audit_question_responses', 'audit_template_questions.id', '=', 'main_audit_question_responses.audit_question_id');
                $query->orderBy('created_at', 'desc');
            }])->get();
        return $questions;
    }
    public function findings()
    {
        return $this->hasOne(MainAuditFinding::class, 'main_audit_id', 'id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'audit_id', 'id');
    }
    // public function members()
    // {
    //     return $this->hasMany(InvestigationMember::class, 'investigation_id', 'id')->where('position', 'member');
    // }
    // public function lead()
    // {
    //     return $this->hasOne(InvestigationMember::class, 'investigation_id', 'id')->where('position', 'lead');
    // }
    // public function questions()
    // {
    //     return $this->hasMany(InvestigationQuestionUser::class, 'investigation_id', 'id');
    // }
    // public function interviews()
    // {
    //     return $this->hasMany(InvestigationInterview::class, 'investigation_id', 'id');
    // }

    // public function findings()
    // {
    //     return $this->hasMany(InvestigationFinding::class, 'investigation_id', 'id');
    // }
}
