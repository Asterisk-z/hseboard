<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['user', 'recipient_organization', 'organization', 'inspection_template_type', 'all_inspectors', 'all_representatives', 'lead_inspector', 'lead_representative', 'schedule', 'findings', 'actions'];
    protected $appends = ['is_ongoing', 'is_pending', 'is_accepted', 'is_completed', 'can_proceed', 'questions'];

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

    public function inspection_template_type()
    {
        return $this->hasOne(InspectionTemplateType::class, 'id', 'inspection_type_id');
    }

    public function getIsPendingAttribute()
    {
        return $this->status == 'pending';
    }

    public function getIsOngoingAttribute()
    {
        return $this->status == 'ongoing';

    }

    public function getIsAcceptedAttribute()
    {
        return $this->status == 'accepted';

    }
    public function getIsCompletedAttribute()
    {
        return $this->status == 'completed';
    }
    public function getCanProceedAttribute()
    {
        return $this->declare_proceed == 'yes';
    }
    public function all_inspectors()
    {
        return $this->hasMany(InspectionMember::class, 'inspection_id', 'id')->where(function ($query) {

            $query->orWhere('position', 'LEAD_INSPECTOR');
            $query->orWhere('position', 'INSPECTION_MEMBER');

        });
    }

    public function all_representatives()
    {
        return $this->hasMany(InspectionMember::class, 'inspection_id', 'id')->where(function ($query) {

            $query->orWhere('position', 'REPRESENTATIVE');
            $query->orWhere('position', 'LEAD_REPRESENTATIVE');

        });
    }

    public function lead_representative()
    {
        return $this->hasOne(InspectionMember::class, 'inspection_id', 'id')->where('position', 'LEAD_REPRESENTATIVE');
    }

    public function lead_inspector()
    {
        return $this->hasOne(InspectionMember::class, 'inspection_id', 'id')->where('position', 'LEAD_INSPECTOR');
    }

    public function schedule()
    {
        return $this->hasOne(InspectionSchedule::class, 'inspection_id', 'id');
    }

    public function getQuestionsAttribute()
    {

        $template = InspectionTemplate::where('id', $this->inspection_template_id)->where('inspection_template_type_id', $this->inspection_type_id)->first();

        $topic_ids = InspectionTemplateQuestions::where('inspection_template_id', $template->id)->where('topic_id', '!=', null)->pluck('topic_id');
        // logger($topic_ids);
        $inspection_id = $this->id;
        $questions = InspectionTemplateQuestions::whereIn('id', $topic_ids)->with([
            'questions' => function (Builder $query) use ($inspection_id) {
                // $query->where('title', 'like', '%code%');
                // inspection_id
                $query->with(['response' => function (Builder $sub_query) use ($inspection_id) {
                    $sub_query->where('inspection_id', $inspection_id);
                }]);
                // $query->leftJoin('main_audit_question_responses', 'inspection_template_questions.id', '=', 'main_audit_question_responses.audit_question_id');
                $query->orderBy('created_at', 'desc');
            }])->get();
        return $questions;
    }
    public function findings()
    {
        return $this->hasOne(InspectionFinding::class, 'inspection_id', 'id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'inspection_id', 'id');
    }

    public function is_completed()
    {
        return ($this->lead_inspector)
        && ($this->lead_representative)
        && ($this->findings)
        && ($this->schedule)
        && ($this->checkAllAnsweredQuestions())
        && (count($this->actions) > 0)
        ? true : false;
    }

    private function checkAllAnsweredQuestions()
    {
        foreach ($this->questions as $topic_question) {
            if (count($topic_question->questions) > 0) {
                foreach ($topic_question->questions as $question) {
                    if (!$question->response) {
                        logger($topic_question->title);
                        logger($question);
                        return false;
                    }
                }
            }
        }

        return true;
    }

}
