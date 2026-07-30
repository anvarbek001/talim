<?php

namespace App\Repositories\Contracts;

use App\Models\DtmTest;
use Illuminate\Database\Eloquent\Collection;

interface DtmTestRepositoryInterface
{
    public function forUser(int $userId): Collection;

    public function create(array $data);

    public function update(DtmTest $dtmTest, array $data);

    public function delete(DtmTest $dtmTest);
}
