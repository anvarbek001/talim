<?php

namespace App\Repositories;

use App\Models\Section;
use App\Repositories\Contracts\SectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class SectionRepository implements SectionRepositoryInterface
{
    public function __construct(
        protected Section $model
    ) {}

    #[Override]
    public function all(): Collection
    {
        return $this->model->with(['user', 'science', 'grade'])->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function update(Section $section, array $data)
    {
        $section->update($data);

        return $section;
    }

    #[Override]
    public function find(int $id)
    {
        return $this->model->where('id', $id)->with(['user', 'science', 'grade'])->first();
    }
}
