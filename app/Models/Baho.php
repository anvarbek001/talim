<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Baho extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'guruh_id',
        'fan_id',
        'dars_id',
        'student_id',
        'baho',
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

    public function dars()
    {
        return $this->belongsTo(Dars::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
