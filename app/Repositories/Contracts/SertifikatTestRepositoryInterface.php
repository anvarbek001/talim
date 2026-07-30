<?php

namespace App\Repositories\Contracts;

use App\Models\SertifikatTest;
use Illuminate\Database\Eloquent\Collection;

interface SertifikatTestRepositoryInterface
{
    public function forUser(int $userId): Collection;

    public function create(array $data);

    public function update(SertifikatTest $sertifikatTest, array $data);

    public function delete(SertifikatTest $sertifikatTest);
}
