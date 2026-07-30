<?php

namespace App\Repositories;

use App\Models\SertifikatTest;
use App\Repositories\Contracts\SertifikatTestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class SertifikatTestRepository implements SertifikatTestRepositoryInterface
{
    public function __construct(
        protected SertifikatTest $model
    ) {}

    #[Override]
    public function forUser(int $userId): Collection
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with(['science', 'questions.options', 'writtenQuestions'])
            ->latest()
            ->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function update(SertifikatTest $sertifikatTest, array $data)
    {
        $sertifikatTest->update($data);

        return $sertifikatTest;
    }

    #[Override]
    public function delete(SertifikatTest $sertifikatTest)
    {
        return $sertifikatTest->delete();
    }
}
