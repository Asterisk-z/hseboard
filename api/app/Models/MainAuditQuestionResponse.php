<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainAuditQuestionResponse extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['is_yes', 'is_nc_minor', 'is_na', 'is_nc_major', 'answer'];

    public function getIsYesAttribute()
    {
        return $this->response == 'yes' ? true : false;
    }

    public function getIsNcMinorAttribute()
    {
        return $this->response == 'nc_minor' ? true : false;
    }

    public function getIsNaAttribute()
    {
        return $this->response == 'na' ? true : false;
    }
    public function getIsNcMajorAttribute()
    {
        return $this->response == 'nc_major' ? true : false;
    }
    public function getAnswerAttribute()
    {
        return $this->response;
    }
}
