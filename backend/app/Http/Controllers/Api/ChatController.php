<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * List conversations for the authenticated user
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Conversation::forUser($userId)
            ->with(['participants' => function ($q) {
                $q->select('users.id', 'users.name', 'users.email', 'users.role');
            }, 'latestMessage'])
            ->get()
            ->map(function ($conv) use ($userId) {
                $other = $conv->getOtherParticipant($userId);
                $unread = $conv->unreadCountFor($userId);

                // Get profile name based on role
                $otherName = $other?->name ?? 'Unknown';
                $otherRole = $other?->role ?? '';

                if ($other) {
                    if ($other->role === 'dosen') {
                        $dosen = $other->dosen;
                        if ($dosen) $otherName = $dosen->full_name;
                    } elseif ($other->role === 'mahasiswa') {
                        $mhs = $other->mahasiswa;
                        if ($mhs) $otherName = $mhs->nama;
                    }
                }

                return [
                    'id' => $conv->id,
                    'other_user' => [
                        'id' => $other?->id,
                        'name' => $otherName,
                        'role' => $otherRole,
                        'email' => $other?->email,
                    ],
                    'last_message' => $conv->latestMessage ? [
                        'body' => $conv->latestMessage->body,
                        'sender_id' => $conv->latestMessage->sender_id,
                        'created_at' => $conv->latestMessage->created_at,
                    ] : null,
                    'unread_count' => $unread,
                    'updated_at' => $conv->latestMessage?->created_at ?? $conv->created_at,
                ];
            })
            ->sortByDesc('updated_at')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Start or get existing conversation with a user
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user()->id;
        $otherUserId = $request->user_id;

        if ($userId == $otherUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memulai chat dengan diri sendiri.',
            ], 422);
        }

        $conversation = Conversation::getOrCreate($userId, $otherUserId);

        // Load participants
        $conversation->load(['participants' => function ($q) {
            $q->select('users.id', 'users.name', 'users.email', 'users.role');
        }]);

        $other = $conversation->getOtherParticipant($userId);
        $otherName = $other?->name ?? 'Unknown';
        if ($other?->role === 'dosen' && $other->dosen) {
            $otherName = $other->dosen->full_name;
        } elseif ($other?->role === 'mahasiswa' && $other->mahasiswa) {
            $otherName = $other->mahasiswa->nama;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $conversation->id,
                'other_user' => [
                    'id' => $other?->id,
                    'name' => $otherName,
                    'role' => $other?->role,
                    'email' => $other?->email,
                ],
            ],
        ]);
    }

    /**
     * Get messages for a conversation
     */
    public function messages(Request $request, Conversation $conversation)
    {
        $userId = $request->user()->id;

        // Ensure user is a participant OR super_admin
        if ($request->user()->role !== 'super_admin' && !$conversation->participants()->where('users.id', $userId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $messages = $conversation->messages()
            ->with(['sender' => function ($q) {
                $q->select('id', 'name', 'role');
            }])
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get()
            ->map(function ($msg) {
                $senderName = $msg->sender?->name ?? 'Unknown';
                if ($msg->sender?->role === 'dosen' && $msg->sender->dosen) {
                    $senderName = $msg->sender->dosen->full_name;
                } elseif ($msg->sender?->role === 'mahasiswa' && $msg->sender->mahasiswa) {
                    $senderName = $msg->sender->mahasiswa->nama;
                }

                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $senderName,
                    'sender_role' => $msg->sender?->role,
                    'body' => $msg->body,
                    'created_at' => $msg->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * List all conversations for admin monitoring
     */
    public function adminIndex(Request $request)
    {
        // Only super_admin allowed
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $conversations = Conversation::with(['participants' => function ($q) {
            $q->select('users.id', 'users.name', 'users.email', 'users.role');
        }, 'latestMessage'])
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->through(function ($conv) {
                $participants = $conv->participants->map(function ($p) {
                    $name = $p->name;
                    if ($p->role === 'dosen' && $p->dosen) {
                        $name = $p->dosen->full_name;
                    } elseif ($p->role === 'mahasiswa' && $p->mahasiswa) {
                        $name = $p->mahasiswa->nama;
                    }
                    return [
                        'id' => $p->id,
                        'name' => $name,
                        'role' => $p->role,
                        'email' => $p->email,
                    ];
                });

                return [
                    'id' => $conv->id,
                    'participants' => $participants,
                    'last_message' => $conv->latestMessage ? [
                        'body' => $conv->latestMessage->body,
                        'sender_id' => $conv->latestMessage->sender_id,
                        'created_at' => $conv->latestMessage->created_at,
                    ] : null,
                    'updated_at' => $conv->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Send a message in a conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $userId = $request->user()->id;

        // Ensure user is a participant
        if (!$conversation->participants()->where('users.id', $userId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'body' => $request->body,
        ]);

        // Get sender display name
        $user = $request->user();
        $senderName = $user->name;
        if ($user->role === 'dosen' && $user->dosen) {
            $senderName = $user->dosen->full_name;
        } elseif ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $senderName = $user->mahasiswa->nama;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $senderName,
                'sender_role' => $user->role,
                'body' => $message->body,
                'created_at' => $message->created_at,
            ],
        ], 201);
    }

    /**
     * Mark conversation as read
     */
    public function markRead(Request $request, Conversation $conversation)
    {
        $userId = $request->user()->id;

        $conversation->participants()->updateExistingPivot($userId, [
            'last_read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation marked as read',
        ]);
    }

    /**
     * Get total unread message count across all conversations
     */
    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Conversation::forUser($userId)->get();
        $total = 0;

        foreach ($conversations as $conv) {
            $total += $conv->unreadCountFor($userId);
        }

        return response()->json([
            'success' => true,
            'count' => $total,
        ]);
    }

    /**
     * Search users available to chat with
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('search', '');
        $currentUser = $request->user();

        $query = User::where('id', '!=', $currentUser->id)
            ->where('is_active', true);

        // Dosen: only allowed to chat with their bimbingan mahasiswa + admins
        if ($currentUser->role === 'dosen') {
            $dosen = $currentUser->dosen;
            $allowedMahasiswaUserIds = [];

            if ($dosen) {
                // Get mahasiswa user_ids linked via pembimbing -> skripsi -> mahasiswa
                $allowedMahasiswaUserIds = \App\Models\Pembimbing::where('dosen_id', $dosen->id)
                    ->where('is_active', true)
                    ->with('skripsi.mahasiswa')
                    ->get()
                    ->pluck('skripsi.mahasiswa.user_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
            }

            $query->where(function ($q) use ($allowedMahasiswaUserIds) {
                $q->where('role', 'admin')
                    ->orWhere(function ($q2) use ($allowedMahasiswaUserIds) {
                        $q2->where('role', 'mahasiswa')
                            ->whereIn('id', $allowedMahasiswaUserIds);
                    });
            });
        } elseif ($currentUser->role === 'mahasiswa') {
            // Mahasiswa: only allowed to chat with their pembimbing dosen + admins
            $mahasiswa = $currentUser->mahasiswa;
            $allowedDosenUserIds = [];

            if ($mahasiswa) {
                $allowedDosenUserIds = \App\Models\Pembimbing::whereHas('skripsi', function ($q) use ($mahasiswa) {
                    $q->where('mahasiswa_id', $mahasiswa->id)
                        ->where('is_active', true);
                })
                    ->where('is_active', true)
                    ->with('dosen')
                    ->get()
                    ->pluck('dosen.user_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
            }

            $query->where(function ($q) use ($allowedDosenUserIds) {
                $q->where('role', 'admin')
                    ->orWhere(function ($q2) use ($allowedDosenUserIds) {
                        $q2->where('role', 'dosen')
                            ->whereIn('id', $allowedDosenUserIds);
                    });
            });
        } elseif ($currentUser->role === 'staff') {
            // Staff: chat with mahasiswa (matching gender) and admin/staff (NOT super_admin)
            $staffGender = $currentUser->jenis_kelamin;
            $query->where(function ($q) use ($staffGender) {
                $q->whereIn('role', ['admin', 'staff'])
                    ->orWhere(function ($q2) use ($staffGender) {
                        $q2->where('role', 'mahasiswa');
                        if ($staffGender) {
                            $q2->whereHas('mahasiswa', function ($mq) use ($staffGender) {
                                $mq->where('jenis_kelamin', $staffGender);
                            });
                        }
                    });
            });
        } else {
            // Admin: can chat with everyone
            $query->whereIn('role', ['admin', 'super_admin', 'dosen', 'mahasiswa', 'staff']);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('dosen', function ($dq) use ($search) {
                        $dq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    })
                    ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                        $mq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->with(['dosen:id,user_id,nama,gelar_depan,gelar_belakang,nip', 'mahasiswa:id,user_id,nama,nim'])
            ->select('id', 'name', 'email', 'role')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                $displayName = $user->name;
                $subtitle = ucfirst($user->role);

                if ($user->role === 'dosen' && $user->dosen) {
                    $displayName = $user->dosen->full_name;
                    $subtitle = 'Dosen · ' . ($user->dosen->nip ?? '');
                } elseif ($user->role === 'mahasiswa' && $user->mahasiswa) {
                    $displayName = $user->mahasiswa->nama;
                    $subtitle = 'Mahasiswa · ' . ($user->mahasiswa->nim ?? '');
                } elseif (in_array($user->role, ['admin', 'super_admin'])) {
                    $subtitle = 'Admin';
                } elseif ($user->role === 'staff') {
                    $subtitle = 'Staff';
                }

                return [
                    'id' => $user->id,
                    'name' => $displayName,
                    'subtitle' => $subtitle,
                    'role' => $user->role,
                    'email' => $user->email,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }
}
