<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Services\TopicService;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function __construct(
        protected TopicService $topicServ
    ) {}

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $this->topicServ->update($request->all(), $id);

        return redirect()->route('lesson')->with('success', 'Mavzu tahrirlandi');
    }

    public function delete(Topic $topic)
    {
        $this->topicServ->delete($topic);

        return redirect()->route('lesson')->with('success', "Mavzu o'chirildi");
    }
}
