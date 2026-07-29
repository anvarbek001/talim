<?php

namespace App\Repositories;

use App\Models\Topic;
use App\Repositories\Contracts\TopicRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;

class TopicRepository implements TopicRepositoryInterface
{
    public function __construct(
        protected Topic $model
    ) {}

    #[Override]
    public function all(): Collection
    {
        return $this->model->with(['user', 'science', 'grade', 'section'])->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
