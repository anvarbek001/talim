<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Dars extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'guruh_id',
        'fan_id',
        'title',
        'desc',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function guruh()
    {
        return $this->belongsTo(Guruh::class);
    }

    public function fan()
    {
        return $this->belongsTo(Fan::class);
    }

    public function bahos()
    {
        return $this->hasMany(Baho::class);
    }

    // app/Models/Dars.php
    public function gradingDeadline(): Carbon
    {
        // yaratilgan kunning oxiri = ertasi kuni 00:00
        return $this->created_at->copy()->addDay()->startOfDay();
    }

    public function isGradable(): bool
    {
        return now()->lt($this->gradingDeadline());
    }
}
