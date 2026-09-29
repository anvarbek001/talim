<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fan extends Model
{
    protected $fillable = [
        'user_id',
        'guruh_id',
        'title',
        'desc',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guruh()
    {
        return $this->belongsTo(Guruh::class);
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