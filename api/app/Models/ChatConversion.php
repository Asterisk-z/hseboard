<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversion extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['recipients'];
    protected $appends = ['main_name', 'main_image'];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'recipients' => $this->recipients,
            'messages' => $this->messages,
            'main_name' => $this->main_name,
            'main_image' => $this->main_image,
            'is_group' => $this->is_group,
            'name' => $this->name,
        ];
    }

    public function recipients()
    {
        return $this->hasMany(ChatRecipient::class, 'conversation_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id', 'id');
    }
    public function getMainNameAttribute()
    {
        $mainName = '';
        if ($this->is_group) {
            $mainName = $this->name;
        } else {
            foreach ($this->recipients as $recipient) {
                if ($recipient->user_id != auth()->user()->id) {
                    $mainName = $recipient->user->full_name;
                    break;
                }
            }
        }
        return $mainName;
    }
    public function getMainImageAttribute()
    {
        $mainName = null;
        if ($this->is_group) {
            $mainName = null;
        } else {
            foreach ($this->recipients as $recipient) {
                if ($recipient->user_id != auth()->user()->id) {
                    // logger($recipient->user->media);
                    $mainName = $recipient->user->media ? $recipient->user->media->full_url : null;
                    break;
                }
            }
        }
        return $mainName;
    }
}
