<?php

namespace App\Repositories;

use App\Models\DtmTest;
use App\Repositories\Contracts\DtmTestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class DtmTestRepository implements DtmTestRepositoryInterface
{
    public function __construct(
        protected DtmTest $model
    ) {}

    #[Override]
    public function forUser(int $userId): Collection
    {
        return $this
            ->model
            ->where('user_id', $userId)
            ->with(['block1Science', 'block2Science', 'grade', 'questions.options'])
            ->latest()
            ->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function update(DtmTest $dtmTest, array $data)
    {
        $dtmTest->update($data);

        return $dtmTest;
    }

    #[Override]
    public function delete(DtmTest $dtmTest)
    {
        return $dtmTest->delete();
    }
}
