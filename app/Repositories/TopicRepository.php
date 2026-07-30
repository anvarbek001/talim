<?php

namespace App\Repositories;

use App\Models\Topic;
use App\Repositories\Contracts\TopicRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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

    #[Override]
    public function find(int $id)
    {
        return $this->model->where(['user_id' => Auth::id(), 'id' => $id])->first();
    }

    #[Override]
    public function update(Topic $topic, array $data)
    {
        return $topic->update($data);
    }

    #[Override]
    public function delete(Topic $topic)
    {
        return $topic->delete();
    }
}
