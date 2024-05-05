<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitToWork extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['issuer', 'holder', 'issuer_organization', 'holder_organization', 'request_type', 'team_members', 'jha'];
    // protected $with = ['all_inspectors', 'all_representatives', 'lead_inspector', 'lead_representative', 'schedule', 'findings', 'actions'];
    protected $appends = ['is_declaration_stage', 'can_issue', 'is_issued',  'is_active', 'is_request_accepted', 'is_request_declined', 'is_request_pending', 'estimated_duration', 'is_jha_accepted', 'is_jha_declined', 'is_jha_pending', 'is_rf_accepted', 'is_rf_declined', 'is_rf_pending'];

    public function getIsDeclarationStageAttribute()
    {
        return $this->send_for_declaration == 'yes' ? true : false;
    }

    public function getIsIssuedAttribute()
    {
        return $this->process_status == 'issued' ? true : false;
    }
    public function getIsActiveAttribute()
    {
        return $this->status == 'active' ? true : false;
    }

    public function getCanIssueAttribute()
    {
        if (!$this->team_members) {
            return false;
        }
        foreach ($this->team_members as $member) {
            if (!$member->is_declared) {
                return false;
            }
        }

        return true;

        // return $this->checkAllTeamMembersHasDeclared;
        // return true;
    }

    public function getIsRequestAcceptedAttribute()
    {
        return $this->request_status == 'accepted' ? true : false;
    }
    public function getIsRequestDeclinedAttribute()
    {
        return $this->request_status == 'declined' ? true : false;
    }
    public function getIsRequestPendingAttribute()
    {
        return $this->request_status == 'pending' ? true : false;
    }

    public function getIsJhaAcceptedAttribute()
    {
        return $this->jha_status == 'accepted' ? true : false;
    }
    public function getIsJhaDeclinedAttribute()
    {
        return $this->jha_status == 'declined' ? true : false;
    }
    public function getIsJhaPendingAttribute()
    {
        return $this->jha_status == 'pending' ? true : false;
    }

    public function getIsRfAcceptedAttribute()
    {
        return $this->request_form_status == 'accepted' ? true : false;
    }
    public function getIsRfDeclinedAttribute()
    {
        return $this->request_form_status == 'action_required' ? true : false;
    }
    public function getIsRfPendingAttribute()
    {
        return $this->request_form_status == 'pending' ? true : false;
    }

    public function getEstimatedDurationAttribute()
    {
        $years = Carbon::create($this->work_start_time)->diffInYears(Carbon::create($this->work_end_time));
        $months = Carbon::create($this->work_start_time)->diffInMonths(Carbon::create($this->work_end_time));
        $days = Carbon::create($this->work_start_time)->diffInDays(Carbon::create($this->work_end_time));
        $hours = Carbon::create($this->work_start_time)->diffInHours(Carbon::create($this->work_end_time));
        $text = 'Years';

        if ($years > 0) {
            $text = $years . " Years";
        } else if ($months > 0) {
            $text = $months . " Months";
        } else if ($days > 0) {
            $text = $days . " Days";
        } else if ($hours > 0) {
            $text = $hours . " Hours";
        } else {
            $text = "-";
        }

        return $text;

    }

    public function issuer()
    {
        return $this->hasOne(User::class, 'id', 'issuer_id');
    }

    public function holder()
    {
        return $this->hasOne(User::class, 'id', 'holder_id');
    }

    public function holder_organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'holder_organization_id');
    }

    public function issuer_organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'issuer_organization_id');
    }

    public function request_type()
    {
        return $this->hasOne(PermitRequestType::class, 'id', 'permit_to_work_request_type_id');
    }

    public function team_members()
    {
        return $this->hasMany(PermitToWorkMember::class, 'permit_to_work_id', 'id');
    }
    public function jha()
    {
        return $this->hasOne(JobHazardAnalysis::class, 'id', 'jha_id');
    }

    private function checkAllTeamMembersHasDeclared()
    {
        // foreach ($this->team_members as $member) {
        // if (!$member->is_declared) {
        //     return false;
        // }
        // }

        return true;
    }
}
