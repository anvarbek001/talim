@extends('layouts.student')

@section('styles')
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow);
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 14px;
    }

    .stat-num {
        font-family: 'Sora', sans-serif;
        font-weight: 800;
        font-size: 1.7rem;
    }

    .stat-lbl {
        color: var(--muted);
        font-size: .82rem;
        margin-top: 2px;
    }

    .rec-scroll {
        display: flex;
        gap: 14px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .rec-card {
        flex: 0 0 auto;
        width: 170px;
        border: 1px solid var(--line);
        border-radius: 14px;
        overflow: hidden;
        transition: .25s;
        display: block;
    }

    .rec-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
    }

    .rec-thumb {
        height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
    }

    .rec-body {
        padding: 10px 12px;
    }

    .rec-title {
        font-size: .8rem;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .rec-sub {
        font-size: .7rem;
        color: var(--muted);
        margin-top: 2px;
    }

    .lb-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--line);
    }

    .lb-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .lb-row.me {
        background: var(--primary-soft);
        border-radius: 12px;
        padding: 10px 12px;
        border-bottom: none;
        margin: 4px 0;
    }

    .lb-rank {
        width: 22px;
        font-family: 'Sora', sans-serif;
        font-weight: 800;
        color: var(--muted);
        font-size: .85rem;
        text-align: center;
    }

    .lb-avatar {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: .72rem;
        font-family: 'Sora', sans-serif;
    }

    .lb-name {
        font-weight: 600;
        font-size: .85rem;
        flex: 1;
    }

    .lb-pts {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: .82rem;
        color: var(--muted);
    }

    .grid-main {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 18px;
    }

    @media (max-width:1100px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-main {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width:767px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-num {
            font-size: 1.3rem;
        }
    }

    @media (max-width:420px) {
        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .stat-num {
            font-size: 1.15rem;
        }

        .stat-lbl {
            font-size: .76rem;
        }

        .rec-card {
            width: 150px;
        }
    }

    .rec-scroll {
        scroll-snap-type: x proximity;
        scroll-padding-left: 2px;
    }

    .rec-card {
        scroll-snap-align: start;
    }

    .subject-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
    }

    .subject-card {
        background: var(--bg-soft);
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 16px 14px;
        text-align: center;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .subject-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }

    .subject-card-main {
        display: block;
    }

    .subject-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin: 0 auto 10px;
    }

    .subject-title {
        font-weight: 700;
        font-size: .88rem;
        color: var(--text);
    }

    .subject-count {
        font-size: .74rem;
        color: var(--muted);
        margin-top: 2px;
    }

    .subject-tests-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        font-size: .74rem;
        font-weight: 600;
        color: var(--primary);
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--line);
    }

    .subject-tests-link:hover {
        color: var(--coral);
    }
@endsection

@section('content')
    <div class="page">
        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card fade-up" style="animation-delay:.02s;">
                <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i
                        class="bi bi-camera-reels"></i></div>
                <div class="stat-num" data-count="{{ $stats['total_lessons'] }}">0</div>
                <div class="stat-lbl">Platformadagi darslar</div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.08s;">
                <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                        class="bi bi-patch-question"></i></div>
                <div class="stat-num" data-count="{{ $stats['attempts_submitted'] }}">0</div>
                <div class="stat-lbl">Topshirilgan testlar</div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.14s;">
                <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i
                        class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-num" data-count="{{ $stats['average_percent'] }}">0</div>
                <div class="stat-lbl">O'rtacha ball (%)</div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.2s;">
                <div class="stat-icon" style="background:var(--amber-soft);color:#8A6100;"><i
                        class="bi bi-bookmark-heart"></i></div>
                <div class="stat-num" data-count="{{ $stats['saved_lessons_count'] }}">0</div>
                <div class="stat-lbl">Saqlangan darslar</div>
            </div>
        </div>

        <!-- SUBJECTS -->
        <div class="card fade-up" style="animation-delay:.06s;">
            <div class="card-head">
                <div class="card-title">Fanlar</div>
                <a href="{{ route('student-lessons.index') }}" class="card-link">Barchasini ko'rish</a>
            </div>
            @if ($sciences->isEmpty())
                <div style="font-size:.84rem;color:var(--muted);">Hozircha fan bo'yicha dars yo'q.</div>
            @else
                <div class="subject-grid">
                    @foreach ($sciences as $science)
                        <div class="subject-card">
                            <a href="{{ route('student-lessons.teachers', $science) }}" class="subject-card-main">
                                <div class="subject-icon" style="background:{{ $science->color }}1A;color:{{ $science->color }};">
                                    <i class="bi {{ $science->icon }}"></i>
                                </div>
                                <div class="subject-title">{{ $science->title }}</div>
                                <div class="subject-count">{{ $science->lessons_count }} ta dars</div>
                            </a>
                            <a href="{{ route('student-tests.index', ['science' => $science->id]) }}" class="subject-tests-link">
                                <i class="bi bi-patch-question"></i> Testlar
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- RECENT LESSONS -->
        <div class="card fade-up" style="animation-delay:.1s;">
            <div class="card-head">
                <div class="card-title">So'nggi darslar</div>
                <a href="{{ route('student-lessons.index') }}" class="card-link">Barchasini ko'rish</a>
            </div>
            @if ($recentLessons->isEmpty())
                <div style="font-size:.84rem;color:var(--muted);">Hozircha dars yo'q.</div>
            @else
                <div class="rec-scroll">
                    @foreach ($recentLessons as $item)
                        <a href="{{ route('student-lessons.show', $item) }}" class="rec-card">
                            <div class="rec-thumb"
                                style="background:linear-gradient(135deg,{{ $item->science->color ?? '#6C5CE7' }},#9C8CFF);">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div class="rec-body">
                                <div class="rec-title">{{ $item->title }}</div>
                                <div class="rec-sub">{{ $item->science->title ?? '—' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid-main">
            <!-- LEFT -->
            <div>
                <div class="card fade-up" style="animation-delay:.16s;">
                    <div class="card-head">
                        <div class="card-title">Saqlangan darslarim</div>
                        <a href="{{ route('student-lessons.index', ['saved' => 1]) }}" class="card-link">Ko'proq</a>
                    </div>
                    @if ($savedLessons->isEmpty())
                        <div style="font-size:.84rem;color:var(--muted);">
                            Hali dars saqlamagansiz — <a href="{{ route('student-lessons.index') }}" style="color:var(--primary);font-weight:600;">darslarni ko'ring</a>.
                        </div>
                    @else
                        <div class="rec-scroll">
                            @foreach ($savedLessons as $item)
                                <a href="{{ route('student-lessons.show', $item) }}" class="rec-card">
                                    <div class="rec-thumb"
                                        style="background:linear-gradient(135deg,{{ $item->science->color ?? '#6C5CE7' }},#9C8CFF);">
                                        <i class="bi bi-bookmark-heart-fill"></i>
                                    </div>
                                    <div class="rec-body">
                                        <div class="rec-title">{{ $item->title }}</div>
                                        <div class="rec-sub">{{ $item->science->title ?? '—' }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card fade-up" style="animation-delay:.22s;">
                    <div class="card-head">
                        <div class="card-title">So'nggi test urinishlari</div>
                        <a href="{{ route('student-statistics.index') }}" class="card-link">Statistikam</a>
                    </div>
                    @forelse ($stats['recent_attempts']->take(4) as $attempt)
                        <div class="lb-row">
                            <div class="lb-avatar" style="background:var(--primary-soft);color:var(--primary);">
                                <i class="bi bi-patch-question" style="font-size:.85rem;"></i>
                            </div>
                            <div class="lb-name">{{ $attempt->testable->title ?? "O'chirilgan test" }}</div>
                            <div class="lb-pts">
                                @if ($attempt->hasPendingGrading())
                                    Baholanmoqda
                                @else
                                    {{ $attempt->score }} / {{ $attempt->max_score }}
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="font-size:.84rem;color:var(--muted);">
                            Hali test topshirmagansiz — <a href="{{ route('student-tests.index') }}" style="color:var(--primary);font-weight:600;">testlarni ko'ring</a>.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT -->
            <div>
                <div class="card fade-up" style="animation-delay:.14s;">
                    <div class="card-head">
                        <div class="card-title">Reyting</div>
                        <a href="{{ route('student-statistics.index') }}" class="card-link">To'liq</a>
                    </div>
                    @forelse ($leaderboard as $index => $row)
                        <div class="lb-row {{ $row['user']->id === auth()->id() ? 'me' : '' }}">
                            <div class="lb-rank">{{ $index + 1 }}</div>
                            <div class="lb-avatar" style="background:var(--primary-soft);color:var(--primary);">{{ mb_substr($row['user']->name, 0, 1) }}</div>
                            <div class="lb-name">{{ $row['user']->id === auth()->id() ? 'Siz — '.$row['user']->name : $row['user']->name }}</div>
                            <div class="lb-pts">{{ $row['total_score'] }}</div>
                        </div>
                    @empty
                        <div style="font-size:.84rem;color:var(--muted);">Hali reyting uchun ma'lumot yo'q.</div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Animated count-up numbers
        document.querySelectorAll('.stat-num[data-count]').forEach(el => {
            const target = parseInt(el.getAttribute('data-count'), 10);
            const duration = 1000;
            const start = performance.now();

            function frame(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target);
                if (progress < 1) requestAnimationFrame(frame);
                else el.textContent = target;
            }
            requestAnimationFrame(frame);
        });
    </script>
@endsection
