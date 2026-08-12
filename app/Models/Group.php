<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'teacher_id',
        'science_id',
        'name',
        'description',
        'invite_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (Group $group) {
            $group->invite_code ??= Str::random(16);
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function science(): BelongsTo
    {
        return $this->belongsTo(Science::class);
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function invites(): HasMany
    {
        return $this->hasMany(GroupInvite::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class);
    }

    public function isTeacher(User $user): bool
    {
        // Qat'iy (===) solishtirish ba'zi hosting/PDO sozlamalarida
        // teacher_id string sifatida qaytganda noto'g'ri false berib,
        // egasining o'zini guruhidan 403 bilan chetlatib qo'yardi.
        return (int) $this->teacher_id === (int) $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->isTeacher($user) || $this->groupMembers()->where('user_id', $user->id)->exists();
    }
}
