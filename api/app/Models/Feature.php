<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    public const CREATE_ACTION = "create_actions";
    public const PREPARE_HSE_STATISTICS = "prepare_hse_statistics";
    public const INTERNAL_HSE_INVESTIGATION = "internal_hse_investigation";
    public const HSE_REINVESTIGATION = "hse_reinvestigation";
    public const EXTERNAL_WITNESS = "external_witness";
    public const ENVIRONMENT_AUDIT_INTERNAL = "environment_audit_internal";
    public const QUALITY_AUDIT_INTERNAL = "quality_audit_internal";
    public const HSE_AUDIT_INTERNAL = "hse_audit_internal";
    public const ENVIRONMENT_AUDIT_EXTERNAL = "environment_audit_external";
    public const QUALITY_AUDIT_EXTERNAL = "quality_audit_external";
    public const HSE_AUDIT_EXTERNAL = "hse_audit_external";
    public const RECORDKEEPING_OSHA_COMPLIANCE = "recordkeeping_osha_compliance";
    public const INTERNAL_INSPECTION = "internal_inspection";
    public const EXTERNAL_INSPECTION = "external_inspection";
    public const JHA_PERMIT_TO_WORK = "jha_permit_to_Work";
    public const INTERNAL_PERMIT_TO_WORK = "internal_permit_to_Work";
    public const EXTERNAL_PERMIT_TO_WORK = "external_permit_to_Work";
    protected $guarded = [];
    protected $with = ['sub_features', 'org_feature', 'currency'];

    public function sub_features()
    {
        return $this->hasMany(Feature::class, 'parent_id', 'id');
    }
    public function org_feature()
    {
        $organization = Organisation::where('uuid', auth()->user()->active_organization)->first();
        return $this->hasOne(OrganizationFeature::class, 'feature_id', 'id')->where('organization_id', $organization->id);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
}
