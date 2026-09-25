<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title'
    ];

    public function guruhs()
    {
        return $this->hasMany(Guruh::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
