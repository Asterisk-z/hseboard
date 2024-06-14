<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservationType extends Model
{
    use HasFactory;
    public const UNSAFE_ACT = 1;
    public const UNSAFE_CONDITION = 2;
    public const NEAR_MISS = 3;
    public const FIRST_AID = 4;
    public const RESTRICTED_WORK = 5;
    public const ROAD_TRAFFIC_ACCIDENT = 6;
    public const MEDICAL_TREATMENT = 7;
    public const LOST_WORKDAY = 8;
    public const DANGEROUS_OCCURANCE = 9;
    public const PERMANENT_PARTIAL_DISABILITY = 10;
    public const PERMANENT_TOTAL_DISABILITY = 11;
    public const FATALITY = 12;
}
