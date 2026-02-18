<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
    ];

    /**
     * The conversation this message belongs to
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * The sender of this message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
