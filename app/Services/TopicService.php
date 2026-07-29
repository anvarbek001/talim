<?php

namespace App\Services;

use App\Repositories\Contracts\TopicRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

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
        if (!$section_id) {
            throw new Exception("Ma'lumotlarda xatolik qaytadan urunib ko'ring", 400);
        }

        return $this->topicRepo->create([
            'user_id' => Auth::user()->id,
            'science_id' => $data['science_id'],
            'grade_id' => $data['grade_id'],
            'section_id' => $section_id,
            'title' => $data['topic_title'],
            'description' => $data['topic_description']
        ]);
    }

    public function update(array $data, int $id)
    {
        $topic = $this->topicRepo->find($id);

        if (!$topic) {
            throw new \Exception('Mavzu topilmadi', 404);
        }

        return $this->topicRepo->update($topic, [
            'science_id' => $data['science_id'],
            'grade_id' => $data['grade_id'],
            'section_id' => $data['section_id'],
            'title' => $data['topic_title'],
            'description' => $data['topic_description'],
        ]);
    }
}
