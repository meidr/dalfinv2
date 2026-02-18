<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [];

    /**
     * Participants in this conversation
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    /**
     * All messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * The latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /**
     * Scope: conversations for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * Get the other participant (for 1-on-1 chats)
     */
    public function getOtherParticipant($userId)
    {
        return $this->participants->where('id', '!=', $userId)->first();
    }

    /**
     * Count unread messages for a specific user
     */
    public function unreadCountFor($userId)
    {
        $participant = $this->participants()->where('users.id', $userId)->first();
        $lastReadAt = $participant?->pivot?->last_read_at;

        $query = $this->messages()->where('sender_id', '!=', $userId);

        if ($lastReadAt) {
            $query->where('created_at', '>', $lastReadAt);
        }

        return $query->count();
    }

    /**
     * Find or create a conversation between two users
     */
    public static function getOrCreate($userId1, $userId2)
    {
        // Find existing conversation between these two users
        $conversation = static::forUser($userId1)
            ->whereHas('participants', function ($q) use ($userId2) {
                $q->where('users.id', $userId2);
            })
            ->first();

        if (!$conversation) {
            $conversation = static::create([]);
            $conversation->participants()->attach([$userId1, $userId2]);
        }

        return $conversation;
    }
}
