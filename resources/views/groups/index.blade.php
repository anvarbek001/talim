@extends('layouts.teacher')

@section('content')
    <div class="page">

        <div class="page-head fade-up mine-head">
            <div>
                <h1>Guruhlarim</h1>
                <p class="page-sub">O'quvchilaringizni guruhlarga birlashtiring va jonli video darslar o'tkazing.</p>
            </div>
            <a href="{{ route('groups.create') }}" class="btn-primary mine-add-btn">
                <i class="bi bi-plus-lg"></i> Yangi guruh
            </a>
        </div>

        @if (session('success'))
            <div class="mine-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if ($groups->isEmpty())
            <div class="mine-empty fade-up">
                <div class="mine-empty-icon"><i class="bi bi-people"></i></div>
                <div class="mine-empty-title">Hali guruh yaratilmagan</div>
                <div class="mine-empty-sub">Birinchi guruhingizni yarating, o'quvchilarni taklif qiling va jonli
                    darslarni boshlang.</div>
                <a href="{{ route('groups.create') }}" class="btn-primary">
                    <i class="bi bi-plus-lg"></i> Guruh yaratish
                </a>
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
                            <div class="group-card-sub">{{ $group->science->title ?? "Fan belgilanmagan" }}</div>
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
            display: flex;
            gap: 12px;
        }
    </style>
@endsection
