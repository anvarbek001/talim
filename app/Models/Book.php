<?php

namespace App\Models;

use App\Contracts\Purchasable;
use App\Models\Concerns\IsPurchasable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model implements Purchasable
{
    use IsPurchasable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(BookFile::class);
    }
}
