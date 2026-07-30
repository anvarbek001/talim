<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Science extends Model
{
    protected $fillable = ['title', 'icon', 'color'];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
