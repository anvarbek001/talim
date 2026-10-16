<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnagrammaGroup extends Model
{
    protected $fillable = ['user_id', 'name', 'token'];

    public function anagrams()
    {
        return $this->hasMany(Anagramma::class, 'group_id');
    }
    public function results()
    {
        return $this->hasMany(AnagrammaResult::class, 'group_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}