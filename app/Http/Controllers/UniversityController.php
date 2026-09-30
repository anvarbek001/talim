<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Baho;
use App\Models\Dars;
use App\Models\Fan;
use App\Models\Guruh;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UniversityController extends Controller
{
    public function index()
    {
        $uid = Auth::id();

        $bahoQuery = fn() => Baho::where('user_id', $uid)->whereNotNull('baho');

        // ===== Asosiy kartalar =====
        $totals = [
            'guruhs'   => Guruh::where('user_id', $uid)->count(),
            'fans'     => Fan::where('user_id', $uid)->count(),
            'darslar'  => Dars::where('user_id', $uid)->count(),
            'students' => Student::where('user_id', $uid)->count(),
            'bahos'    => $bahoQuery()->count(),
            'avg'      => ($a = $bahoQuery()->avg('baho')) ? round($a, 1) : null,
        ];

        $thisMonth = [
            'guruhs'   => Guruh::where('user_id', $uid)->where('created_at', '>=', now()->startOfMonth())->count(),
            'fans'     => Fan::where('user_id', $uid)->where('created_at', '>=', now()->startOfMonth())->count(),
            'darslar'  => Dars::where('user_id', $uid)->where('created_at', '>=', now()->startOfMonth())->count(),
            'students' => Student::where('user_id', $uid)->where('created_at', '>=', now()->startOfMonth())->count(),
            'bahos'    => $bahoQuery()->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        // ===== Baholar taqsimoti (2..5) =====
        $raw = $bahoQuery()->selectRaw('baho, COUNT(*) as c')->groupBy('baho')->pluck('c', 'baho');
        $distribution = collect([2, 3, 4, 5])->map(fn($g) => (int) ($raw[$g] ?? 0))->all();

        // ===== Oxirgi 7 kun =====
        $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->startOfDay());

        $perDay = $bahoQuery()
            ->where('created_at', '>=', $days->first())
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $weekLabels = $days->map(fn($d) => $d->format('d.m'))->all();
        $weekValues = $days->map(fn($d) => (int) ($perDay[$d->toDateString()] ?? 0))->all();

        // ===== Guruhlar bo'yicha =====
        $guruhAvg = $bahoQuery()
            ->selectRaw('guruh_id, AVG(baho) as a, COUNT(*) as c')
            ->groupBy('guruh_id')->get()->keyBy('guruh_id');

        $guruhs = Guruh::where('user_id', $uid)
            ->with('course:id,title')
            ->withCount('students')
            ->get()
            ->map(fn($g) => [
                'title'    => $g->title,
                'course'   => $g->course->title ?? '—',
                'students' => $g->students_count,
                'count'    => (int) ($guruhAvg[$g->id]->c ?? 0),
                'avg'      => isset($guruhAvg[$g->id]) ? round($guruhAvg[$g->id]->a, 1) : null,
            ]);

        // ===== Fanlar bo'yicha =====
        $fanAvg = $bahoQuery()
            ->selectRaw('fan_id, AVG(baho) as a, COUNT(*) as c')
            ->groupBy('fan_id')->get()->keyBy('fan_id');

        $dCount = Dars::where('user_id', $uid)
            ->selectRaw('fan_id, COUNT(*) as c')->groupBy('fan_id')->pluck('c', 'fan_id');

        $fans = Fan::where('user_id', $uid)->with('guruh:id,title')->get()
            ->map(fn($f) => [
                'title'  => $f->title,
                'guruh'  => $f->guruh->title ?? '—',
                'darslar' => (int) ($dCount[$f->id] ?? 0),
                'count'  => (int) ($fanAvg[$f->id]->c ?? 0),
                'avg'    => isset($fanAvg[$f->id]) ? round($fanAvg[$f->id]->a, 1) : null,
            ]);

        // ===== Eng yaxshi va e'tibor kerak bo'lgan talabalar =====
        $studentAvg = $bahoQuery()
            ->selectRaw('student_id, AVG(baho) as a, COUNT(*) as c')
            ->groupBy('student_id')->get();

        $names = Student::whereIn('id', $studentAvg->pluck('student_id'))
            ->with('guruh:id,title')->get()->keyBy('id');

        $rank = $studentAvg->map(fn($r) => [
            'name'  => $names[$r->student_id]->name ?? '—',
            'guruh' => $names[$r->student_id]->guruh->title ?? '—',
            'avg'   => round($r->a, 1),
            'count' => (int) $r->c,
        ]);

        $topStudents = $rank->sortByDesc('avg')->take(5)->values();
        $weakStudents = $rank->sortBy('avg')->take(5)->values();

        $ungradedStudents = Student::where('user_id', $uid)
            ->whereNotIn('id', Baho::where('user_id', $uid)->whereNotNull('baho')->select('student_id'))
            ->count();

        // ===== Bugungi darslar (baholash ochiq) =====
        $todayDarslar = Dars::where('user_id', $uid)
            ->whereDate('created_at', today())
            ->with(['fan:id,title', 'guruh:id,title'])
            ->latest()->get();

        $gradedPerDars = Baho::whereIn('dars_id', $todayDarslar->pluck('id'))
            ->whereNotNull('baho')
            ->selectRaw('dars_id, COUNT(*) as c')->groupBy('dars_id')->pluck('c', 'dars_id');

        $studentsPerGuruh = Student::whereIn('guruh_id', $todayDarslar->pluck('guruh_id'))
            ->selectRaw('guruh_id, COUNT(*) as c')->groupBy('guruh_id')->pluck('c', 'guruh_id');

        $today = $todayDarslar->map(fn($d) => [
            'id'     => $d->id,
            'fan_id' => $d->fan_id,
            'title'  => $d->title,
            'fan'    => $d->fan->title ?? '—',
            'guruh'  => $d->guruh->title ?? '—',
            'graded' => (int) ($gradedPerDars[$d->id] ?? 0),
            'total'  => (int) ($studentsPerGuruh[$d->guruh_id] ?? 0),
        ]);

        return view('universities.index', compact(
            'totals',
            'thisMonth',
            'distribution',
            'weekLabels',
            'weekValues',
            'guruhs',
            'fans',
            'topStudents',
            'weakStudents',
            'ungradedStudents',
            'today'
        ));
    }

    public function show()
    {
        $me = auth()->user();

        // faqat maxsus login egalari va faqat universiteti bor foydalanuvchi
        abort_unless($me->canViewUniversity(), 403);

        // 1) shu universitetning o'qituvchilari
        $teachers = User::where('university_id', $me->university_id)
            ->whereIn('id', Guruh::select('user_id'))
            ->orderBy('name')
            ->get();

        // 2) ularning guruhlari va talabalari
        $guruhs = Guruh::whereIn('user_id', $teachers->pluck('id'))
            ->with('course:id,title')
            ->get()
            ->groupBy('user_id');

        $guruhIds = $guruhs->flatten()->pluck('id');

        $students = Student::whereIn('guruh_id', $guruhIds)
            ->orderBy('name')
            ->get()
            ->groupBy('guruh_id');

        // 3) barcha baholar (bitta so'rov)
        $bahos = Baho::whereIn('guruh_id', $guruhIds)
            ->whereNotNull('baho')
            ->with(['fan:id,title', 'dars:id,title,created_at'])
            ->get();

        $byStudent = $bahos->groupBy('student_id');
        $byGuruh   = $bahos->groupBy('guruh_id');

        $avg = fn($c) => $c->count() ? round($c->avg('baho'), 1) : null;

        // 4) talabalar bo'yicha batafsil ma'lumot (modal uchun ham)
        $studentInfo = $byStudent->map(fn($rows) => [
            'avg'   => $avg($rows),
            'count' => $rows->count(),
            'fans'  => $rows->groupBy('fan_id')->map(fn($items) => [
                'fan'    => $items->first()->fan->title ?? '—',
                'avg'    => $avg($items),
                'count'  => $items->count(),
                'grades' => $items->sortBy('dars_id')->map(fn($b) => [
                    'dars' => $b->dars->title ?? '—',
                    'baho' => $b->baho,
                    'date' => optional($b->dars?->created_at)->format('d.m.Y'),
                ])->values(),
            ])->values(),
        ]);

        // 5) o'qituvchi -> guruh -> talaba daraxti
        $tree = $teachers->map(function ($t) use ($guruhs, $students, $byGuruh, $studentInfo, $avg) {
            $teacherGuruhs = $guruhs[$t->id] ?? collect();

            $groups = $teacherGuruhs->map(function ($g) use ($students, $byGuruh, $studentInfo, $avg) {
                $gRows = $byGuruh[$g->id] ?? collect();

                return [
                    'title'    => $g->title,
                    'course'   => $g->course->title ?? '—',
                    'avg'      => $avg($gRows),
                    'count'    => $gRows->count(),
                    'students' => ($students[$g->id] ?? collect())->map(fn($s) => [
                        'id'    => $s->id,
                        'name'  => $s->name,
                        'photo' => $s->photo,
                        'avg'   => $studentInfo[$s->id]['avg'] ?? null,
                        'count' => $studentInfo[$s->id]['count'] ?? 0,
                        'fans'  => collect($studentInfo[$s->id]['fans'] ?? [])->map(fn($f) => [
                            'fan' => $f['fan'],
                            'avg' => $f['avg'],
                        ]),
                    ]),
                ];
            });

            // o'qituvchining barcha guruhlaridagi baholar
            $teacherRows = $teacherGuruhs
                ->flatMap(fn($g) => $byGuruh[$g->id] ?? collect());

            return [
                'id'       => $t->id,
                'name'     => $t->name,
                'groups'   => $groups,
                'students' => $groups->sum(fn($g) => $g['students']->count()),
                'count'    => $teacherRows->count(),
                'avg'      => $avg($teacherRows),
            ];
        });

        $totals = [
            'teachers' => $teachers->count(),
            'guruhs'   => $guruhIds->count(),
            'students' => $students->flatten()->count(),
            'bahos'    => $bahos->count(),
            'avg'      => $avg($bahos),
        ];

        return view('universities.show', compact('tree', 'totals', 'studentInfo'));
    }
}