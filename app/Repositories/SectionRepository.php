<?php

namespace App\Repositories;

use App\Models\Section;
use App\Repositories\Contracts\SectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Override;
use Illuminate\Support\Facades\Auth;

class SectionRepository implements SectionRepositoryInterface
{
    public function __construct(
        protected Section $model
    ) {}

    #[Override]
    public function all(): Collection
    {
        $query = $this->model->with(['user', 'science', 'grade']);

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
