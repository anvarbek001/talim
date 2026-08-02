<?php

namespace App\Services;

use App\Models\Section;
use App\Repositories\Contracts\SectionRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

class SectionService
{
    public function __construct(
        protected SectionRepositoryInterface $sectionRepo
    ) {}

    public function all()
    {
        return $this->sectionRepo->all();
    }

    public function create(array $data)
    {
        return $this->sectionRepo->create([
            'user_id' => Auth::user()->id,
            'science_id' => $data['science_id'],
            'grade_id' => $data['grade_id'],
            'title' => $data['section_title'],
            'description' => $data['section_description'] ?? null,
            'price' => $data['price'] ?? 0,
        ]);
    }

    public function update(Section $section, array $data): Section
    {
        if ($section->user_id !== Auth::id()) {
            throw new Exception('Bu bo\'lim ustida amaliyot bajara olmaysiz', 403);
        }

        return $this->sectionRepo->update($section, [
            'price' => $data['price'],
        ]);
    }

    public function find(int $id)
    {
        return $this->sectionRepo->find($id);
    }
}
