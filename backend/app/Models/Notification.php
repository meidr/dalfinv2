<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    /**
     * Target user (admin) for the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: unread notifications only
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: notifications for a specific user or broadcast (user_id = null)
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id')
                ->orWhere('user_id', $userId);
        });
    }

    /**
     * Create a broadcast notification (to all admins)
     */
    public static function broadcast(string $type, string $title, string $message, ?array $data = null)
    {
        return static::create([
            'user_id' => null,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
