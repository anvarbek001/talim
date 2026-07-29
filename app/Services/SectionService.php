<?php

namespace App\Services;

use App\Repositories\Contracts\SectionRepositoryInterface;
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
            'description' => $data['section_description']
        ]);
    }

    public function find(int $id)
    {
        return $this->sectionRepo->find($id);
    }
}
