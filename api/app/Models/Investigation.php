<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['user', 'observation', 'organization', 'members', 'lead', 'all_members', 'questions', 'interviews', 'findings', 'actions', 'report'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function observation()
    {
        return $this->hasOne(Observation::class, 'id', 'observation_id');
    }

    public function organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'organization_id');
    }

    public function all_members()
    {
        return $this->hasMany(InvestigationMember::class, 'investigation_id', 'id');
    }
    public function members()
    {
        return $this->hasMany(InvestigationMember::class, 'investigation_id', 'id')->where('position', 'member');
    }
    public function lead()
    {
        return $this->hasOne(InvestigationMember::class, 'investigation_id', 'id')->where('position', 'lead');
    }
    public function questions()
    {
        return $this->hasMany(InvestigationQuestionUser::class, 'investigation_id', 'id');
    }
    public function interviews()
    {
        return $this->hasMany(InvestigationInterview::class, 'investigation_id', 'id');
    }

    public function findings()
    {
        return $this->hasMany(InvestigationFinding::class, 'investigation_id', 'id');
    }
    public function evidenceFindings()
    {
        return $this->hasMany(InvestigationFinding::class, 'investigation_id', 'id')->where('type', 'evidence');
    }
    public function rootFindings()
    {
        return $this->hasMany(InvestigationFinding::class, 'investigation_id', 'id')->where('type', 'root');
    }
    public function immediateFindings()
    {
        return $this->hasMany(InvestigationFinding::class, 'investigation_id', 'id')->where('type', 'immediate');
    }
    public function conclusionFindings()
    {
        return $this->hasMany(InvestigationFinding::class, 'investigation_id', 'id')->where('type', 'conclusion');
    }
    public function actions()
    {
        return $this->hasMany(Action::class, 'investigation_id', 'id');
    }
    public function report()
    {
        return $this->hasOne(InvestigationReport::class, 'investigation_id', 'id');
    }

    public function is_completed()
    {
        return ($this->lead)
        && (count($this->actions) > 0)
        // && count($this->findings) > 0
        // && count($this->interviews) > 0
        // && count($this->questions) > 0
        && count($this->members) > 0 ? true : false;
    }
}
