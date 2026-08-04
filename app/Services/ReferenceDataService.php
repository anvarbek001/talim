<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Science;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Sciences and grades are near-static taxonomies (seeded once, rarely
 * edited) that get queried on almost every teacher/student page — caching
 * them avoids re-running the same "select * from sciences" on every request.
 */
class ReferenceDataService
{
    public const TTL_SECONDS = 3600;

    public function sciences(): Collection
    {
        return Cache::remember('reference.sciences', self::TTL_SECONDS, fn () => Science::all());
    }

    public function grades(): Collection
    {
        return Cache::remember('reference.grades', self::TTL_SECONDS, fn () => Grade::all());
    }

    /**
     * Call after any science/grade create, update, or delete so the cached
     * lists don't serve stale data until the TTL expires on its own.
     */
    public function forget(): void
    {
        Cache::forget('reference.sciences');
        Cache::forget('reference.grades');
    }
}
