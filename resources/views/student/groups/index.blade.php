@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Guruhlarim</h1>
                <p class="page-sub">O'qituvchilaringiz sizni qo'shgan guruhlar va jonli darslar shu yerda
                    to'planadi.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mine-alert fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif

        @if ($upcomingSessions->isNotEmpty())
            <div class="panel fade-up" style="margin-bottom:22px;">
                <div class="panel-head"><h2><i class="bi bi-camera-video-fill"></i> Yaqin/jonli darslar</h2></div>
                <div class="session-list">
                    @foreach ($upcomingSessions as $session)
                        <div class="session-item">
                            <div class="session-item-main">
                                <span class="status-dot status-{{ $session->status }}"></span>
                                <div>
                                    <div class="session-item-title">{{ $session->title }}</div>
                                    <div class="session-item-meta">
                                        {{ $session->group->name }} ·
                                        @if ($session->status === 'live')
                                            Hozir jonli efirda
                                        @elseif ($session->scheduled_at)
                                            {{ $session->scheduled_at->translatedFormat('d-M, H:i') }} ga rejalashtirilgan
                                        @else
                                            Istalgan vaqtda boshlanishi mumkin
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('live-sessions.room', $session) }}" class="btn-primary btn-sm">
                                <i class="bi bi-box-arrow-in-right"></i> Qo'shilish
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($groups->isEmpty())
            <div class="mine-empty fade-up">
                <div class="mine-empty-icon"><i class="bi bi-people"></i></div>
                <div class="mine-empty-title">Hali hech qanday guruhga qo'shilmagansiz</div>
                <div class="mine-empty-sub">O'qituvchingizdan taklif havolasi yoki email orqali taklif so'rang.</div>
            </div>
        @else
            <div class="group-grid">
                @foreach ($groups as $group)
                    <a href="{{ route('groups.show', $group) }}" class="group-card fade-up">
                        <div class="group-card-icon" style="background:{{ $group->science->color ?? '#6C5CE7' }}22;color:{{ $group->science->color ?? '#6C5CE7' }};">
                            <i class="bi {{ $group->science->icon ?? 'bi-people' }}"></i>
                        </div>
                        <div class="group-card-body">
                            <div class="group-card-title">{{ $group->name }}</div>
                            <div class="group-card-sub">O'qituvchi: {{ $group->teacher->name }}</div>
                            <div class="group-card-meta">
                                <span><i class="bi bi-person-fill"></i> {{ $group->members_count }} a'zo</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .panel-head h2 {
            font-size: 1.02rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            color: var(--text);
        }

        .session-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .session-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .session-item-main {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-live {
            background: var(--coral);
            box-shadow: 0 0 0 4px var(--coral-soft);
        }

        .status-scheduled {
            background: var(--amber);
        }

        .session-item-title {
            font-weight: 700;
            font-size: .9rem;
            color: var(--text);
        }

        .session-item-meta {
            font-size: .76rem;
            color: var(--muted);
        }

        .btn-sm {
            padding: 8px 14px !important;
            font-size: .8rem !important;
        }

        .group-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 18px;
        }

        .group-card {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow-sm);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .group-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .group-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .group-card-title {
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
        }

        .group-card-sub {
            font-size: .82rem;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .group-card-meta {
            font-size: .78rem;
            color: var(--muted);
            font-weight: 600;
        }
    </style>
@endsection
