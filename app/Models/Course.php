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

    public function darses()
    {
        return $this->hasMany(Dars::class);
    }

    public function bahos()
    {
        return $this->hasMany(Baho::class);
    }
}
