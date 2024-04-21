<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MainAuditDocument extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['media', 'uploaded_by'];
    protected $appends = ['is_pending', 'is_accepted', 'is_uploaded', 'is_rejected'];
    public function media(): MorphOne
    {
        return $this->morphOne(Media::class, 'model');
    }
    public function uploaded_by()
    {
        return $this->hasOne(User::class, 'id', 'recipient_user_id');
    }

    public function getIsPendingAttribute()
    {
        return $this->status == 'pending' ? true : false;
    }

    public function getIsAcceptedAttribute()
    {
        return $this->status == 'accepted' ? true : false;
    }
    public function getIsRejectedAttribute()
    {
        return $this->status == 'rejected' ? true : false;
    }

    public function getIsUploadedAttribute()
    {
        return $this->status == 'uploaded' ? true : false;
    }
}
