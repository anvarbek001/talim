<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guruh extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
