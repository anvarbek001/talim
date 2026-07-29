<?php

namespace App\Services;

use App\Repositories\Contracts\TopicRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TopicService
{
    public function __construct(
        protected TopicRepositoryInterface $topicRepo
    ) {}

    public function all()
    {
        return $this->topicRepo->all();
    }

    public function create(array $data, int $section_id)
    {
        return $this->topicRepo->create([
            'user_id' => Auth::user()->id,
            'science_id' => $data['science_id'],
            'grade_id' => $data['grade_id'],
            'section_id' => $section_id,
            'title' => $data['topic_title'],
            'description' => $data['topic_description']
        ]);
    }
}
