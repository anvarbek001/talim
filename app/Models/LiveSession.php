<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LiveSession extends Model
{
    protected $fillable = [
        'group_id',
        'teacher_id',
        'title',
        'room_name',
        'scheduled_at',
        'started_at',
        'ended_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LiveSession $session) {
            $session->room_name ??= 'dq-'.Str::random(24);
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LiveSessionParticipant::class);
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function hasEnded(): bool
    {
        return in_array($this->status, ['ended', 'canceled'], true);
    }

    /**
     * Dars boshlanmagan (scheduled) yoki hozir davom etayotgan (live) bo'lsa
     * xonaga kirish mumkin — tugagan/bekor qilingan darsga kirib bo'lmaydi.
     */
    public function canJoin(): bool
    {
        return in_array($this->status, ['scheduled', 'live'], true);
    }
}
