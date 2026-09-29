<?php

namespace App\Models;

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
}