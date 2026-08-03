<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookFile extends Model
{
    protected $fillable = [
        'book_id',
        'file_path',
        'original_name',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
