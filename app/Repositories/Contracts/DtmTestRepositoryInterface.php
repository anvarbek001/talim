<?php

namespace App\Repositories\Contracts;

use App\Models\DtmTest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DtmTestRepositoryInterface
{
    /**
     * @param  array{q?: string, teacher_id?: int, per_page?: int, page_name?: string}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator;

    /**
     * @param  array{q?: string}  $filters
     */
    public function forUser(int $userId, array $filters = []): Collection;

    public function create(array $data);

    public function update(DtmTest $dtmTest, array $data);

    public function delete(DtmTest $dtmTest);
}
