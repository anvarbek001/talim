<?php

namespace App\Repositories\Contracts;

use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;

interface SectionRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data);

    public function update(Section $section, array $data);

    public function find(int $id);
}
