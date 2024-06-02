<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['sender'];

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }
}
