<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YesNoResult extends Model
{
    protected $table = 'yesno_results';

    protected $fillable = ['user_id', 'player_name', 'score', 'correct', 'total'];
}