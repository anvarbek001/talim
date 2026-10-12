<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anagramma extends Model
{
    protected $table = 'anagrammas';

    protected $fillable = ['user_id', 'word', 'hint'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}