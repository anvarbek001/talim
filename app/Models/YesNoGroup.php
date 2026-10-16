<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YesNoGroup extends Model
{
    protected $table = 'yesno_groups';
    protected $fillable = ['user_id', 'name', 'token'];

    public function questions()
    {
        return $this->hasMany(YesNoQuestion::class, 'group_id');
    }
    public function results()
    {
        return $this->hasMany(YesNoResult::class, 'group_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}