<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YesNoQuestion extends Model
{
    protected $table = 'yesno_questions';

    protected $fillable = ['user_id', 'statement', 'answer', 'explanation'];

    protected $casts = ['answer' => 'boolean'];
}