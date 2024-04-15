<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationQuestionUser extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['user', 'responder', 'question'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function responder()
    {
        return $this->hasOne(User::class, 'id', 'responder_id');
    }
    public function question()
    {
        return $this->hasOne(InvestigationQuestion::class, 'id', 'question_id');
    }
}
