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
        $query = $this->model->with(['user', 'science', 'grade', 'section']);

        $user = Auth::user();
        if ($user && $user->hasRole('admin')) {
            return $query->get();
        }

        return $query->where('user_id', Auth::id())->get();
    }

    #[Override]
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    #[Override]
    public function find(int $id)
    {
        return $this->model->with(['user', 'science', 'grade', 'section'])->find($id);
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
