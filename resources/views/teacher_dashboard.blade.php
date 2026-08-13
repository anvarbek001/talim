@extends('layouts.teacher')
@section('content')
    @php
        $maxWeeklyCount = max(1, $weeklyActivity->max('count'));
        $revenueUp = $stats['revenue_delta_percent'] >= 0;
    @endphp
    <div class="page">
        <!-- STAT CARDS -->
        <div class="stats-grid">
            @if (config('features.lessons_enabled'))
                <div class="stat-card fade-up" style="animation-delay:.02s;">
                    <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                            class="bi bi-camera-reels"></i></div>
                    <div class="stat-num" data-count="{{ $stats['lessons_count'] }}">0</div>
                    <div class="stat-lbl">Jami darslarim</div>
                    <div class="stat-delta {{ $stats['lessons_delta'] > 0 ? 'up' : '' }}">
                        @if ($stats['lessons_delta'] > 0)
                            <i class="bi bi-arrow-up-short"></i> +{{ $stats['lessons_delta'] }} shu oy
                        @else
                            Shu oy qo'shilmagan
                        @endif
                    </div>
                </div>
            @endif
            <div class="stat-card fade-up" style="animation-delay:.08s;">
                <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i class="bi bi-people"></i>
                </div>
                <div class="stat-num" data-count="{{ $stats['subscribers_count'] }}">0</div>
                <div class="stat-lbl">Obunachilarim</div>
                <div class="stat-delta {{ $stats['subscribers_delta'] > 0 ? 'up' : '' }}">
                    @if ($stats['subscribers_delta'] > 0)
                        <i class="bi bi-arrow-up-short"></i> +{{ $stats['subscribers_delta'] }} shu oy
                    @else
                        Shu oy yangisi yo'q
                    @endif
                </div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.14s;">
                <div class="stat-icon" style="background:var(--amber-soft);color:#8A6100;"><i class="bi bi-mortarboard"></i>
                </div>
                <div class="stat-num" data-count="{{ $stats['students_count'] }}">0</div>
                <div class="stat-lbl">O'quvchilarim</div>
                <div class="stat-delta" style="color:var(--muted);">Testga uringanlar soni</div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.2s;">
                <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i
                        class="bi bi-wallet2"></i></div>
                <div class="stat-num" data-count="{{ $stats['monthly_revenue'] }}">0</div>
                <div class="stat-lbl">Oylik daromad (so'm)</div>
                <div class="stat-delta {{ $revenueUp ? 'up' : 'down' }}">
                    @if ($stats['revenue_delta_percent'] != 0)
                        <i class="bi bi-arrow-{{ $revenueUp ? 'up' : 'down' }}-short"></i>
                        {{ $revenueUp ? '+' : '' }}{{ $stats['revenue_delta_percent'] }}%
                    @else
                        O'tgan oy bilan bir xil
                    @endif
                </div>
            </div>
        </div>

        <div class="grid-main">
            <!-- LEFT -->
            <div>
                @if (config('features.lessons_enabled'))
                    <div class="card fade-up" style="animation-delay:.1s;">
                        <div class="card-head">
                            <div class="card-title">So'nggi darslar</div>
                            <a href="{{ route('lessons.mine') }}" class="card-link">Barchasini ko'rish</a>
                        </div>

                        @forelse ($recentLessons as $lesson)
                            <div class="lesson-row">
                                <div class="lesson-thumb" style="background:linear-gradient(135deg,var(--primary),#9C8CFF);">
                                    <i class="bi bi-play-fill"></i>
                                </div>
                                <div class="lesson-info">
                                    <div class="lesson-title">{{ $lesson->title }}</div>
                                    <div class="lesson-sub">{{ $lesson->science->title ?? "Fan belgilanmagan" }} ·
                                        {{ $lesson->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="lesson-views"><i class="bi bi-bookmark-fill"></i> {{ $lesson->saved_by_users_count }}</div>
                            </div>
                        @empty
                            <p style="color:var(--muted);font-size:.88rem;padding:8px 0;">Hali dars qo'shmagansiz.</p>
                        @endforelse
                    </div>
                @endif

                <div class="card fade-up" style="animation-delay:.16s;">
                    <div class="card-head">
                        <div class="card-title">Haftalik faollik</div>
                        <span class="card-link" style="color:var(--muted);">Testga urinishlar soni</span>
                    </div>
                    <div class="week-chart">
                        @foreach ($weeklyActivity as $i => $day)
                            <div class="week-col">
                                <div class="week-bar"
                                    style="height:{{ max(6, round($day['count'] / $maxWeeklyCount * 100)) }}%;animation-delay:{{ .05 + $i * .05 }}s;"
                                    title="{{ $day['count'] }} ta urinish"></div>
                                <div class="week-lbl">{{ $day['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div>
                <div class="card fade-up" style="animation-delay:.14s;">
                    <div class="card-head">
                        <div class="card-title">So'nggi xaridlar</div>
                    </div>

                    @forelse ($recentPurchases as $purchase)
                        <div class="comment-row">
                            <div class="comment-avatar" style="background:var(--primary);">
                                {{ mb_substr($purchase->user->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div class="comment-name">{{ $purchase->user->name ?? "O'chirilgan foydalanuvchi" }}</div>
                                <div class="comment-text">
                                    <strong>{{ $typeLabels[$purchase->purchasable_type] ?? '' }}</strong> —
                                    {{ $purchase->purchasable->title ?? '—' }}
                                    ({{ number_format($purchase->price, 0, '.', ' ') }} so'm)
                                </div>
                                <div class="comment-meta">{{ $purchase->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="color:var(--muted);font-size:.88rem;padding:8px 0;">Hali xarid bo'lmagan.</p>
                    @endforelse
                </div>

                <div class="card fade-up" style="animation-delay:.22s;">
                    <div class="card-head">
                        <div class="card-title">Tezkor amallar</div>
                    </div>
                    @if (config('features.lessons_enabled'))
                        <a href="{{ route('lessons.mine') }}" class="qa-btn">
                            <div class="qa-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                                    class="bi bi-cloud-upload"></i></div> Video yuklash
                        </a>
                    @endif
                    <a href="{{ route('tests.index') }}" class="qa-btn">
                        <div class="qa-icon" style="background:var(--mint-soft);color:var(--mint);"><i
                                class="bi bi-patch-question"></i></div> Test yaratish
                    </a>
                    <a href="{{ route('books.mine') }}" class="qa-btn">
                        <div class="qa-icon" style="background:var(--amber-soft);color:#8A6100;"><i
                                class="bi bi-journal-bookmark"></i></div> Kitob yuklash
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
