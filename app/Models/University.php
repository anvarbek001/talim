<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $fillable = [
        'title'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
