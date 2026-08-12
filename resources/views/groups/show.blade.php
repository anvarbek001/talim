@extends($isTeacher ? 'layouts.teacher' : 'layouts.student')

@section('content')
    <div class="page">

        <div class="page-head fade-up mine-head">
            <div>
                <h1>{{ $group->name }}</h1>
                <p class="page-sub">
                    {{ $group->science->title ?? "Fan belgilanmagan" }} ·
                    {{ $group->members->count() }} a'zo ·
                    O'qituvchi: {{ $group->teacher->name }}
                </p>
                @if ($group->description)
                    <p class="page-sub">{{ $group->description }}</p>
                @endif
            </div>
            @if ($isTeacher)
                <form method="POST" action="{{ route('groups.destroy', $group) }}"
                    onsubmit="return confirm('Guruhni o\'chirmoqchimisiz? Bu amalni ortga qaytarib bo\'lmaydi.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-outline"><i class="bi bi-trash"></i> Guruhni o'chirish</button>
                </form>
            @endif
        </div>

        @if (session('success'))
            <div class="mine-alert fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mine-alert mine-alert-danger fade-up"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif

        <div class="group-grid-2">
            <div class="group-col">
                {{-- JONLI DARSLAR --}}
                <div class="panel fade-up">
                    <div class="panel-head">
                        <h2><i class="bi bi-camera-video-fill"></i> Jonli darslar</h2>
                    </div>

                    @if ($isTeacher)
                        <form method="POST" action="{{ route('live-sessions.store', $group) }}" class="session-form">
                            @csrf
                            <input type="text" name="title" placeholder="Dars mavzusi (masalan: Kvadrat tenglamalar)" required value="{{ old('title') }}">
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}">
                            <button type="submit" class="btn-primary"><i class="bi bi-lightning-fill"></i> Boshlash / rejalashtirish</button>
                        </form>
                        <p class="hint-text">Vaqtni bo'sh qoldirsangiz, darsni darhol boshlaysiz. Vaqt tanlasangiz —
                            dars shu vaqtga rejalashtiriladi va istalgan payt "Boshlash" tugmasi bilan ochasiz.</p>
                        @error('title')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error('scheduled_at')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    @endif

                    @if ($sessions->isEmpty())
                        <div class="empty-inline">Hali dars rejalashtirilmagan.</div>
                    @else
                        <div class="session-list">
                            @foreach ($sessions as $session)
                                <div class="session-item">
                                    <div class="session-item-main">
                                        <span class="status-dot status-{{ $session->status }}"></span>
                                        <div>
                                            <div class="session-item-title">{{ $session->title }}</div>
                                            <div class="session-item-meta">
                                                @if ($session->status === 'live')
                                                    Hozir jonli efirda
                                                @elseif ($session->scheduled_at)
                                                    {{ $session->scheduled_at->translatedFormat('d-M, H:i') }} ga rejalashtirilgan
                                                @elseif ($session->status === 'scheduled')
                                                    Istalgan vaqtda boshlanishi mumkin
                                                @elseif ($session->status === 'ended')
                                                    Yakunlangan · {{ $session->ended_at?->diffForHumans() }}
                                                @else
                                                    Bekor qilingan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="session-item-actions">
                                        @if ($session->canJoin())
                                            <a href="{{ route('live-sessions.room', $session) }}" class="btn-primary btn-sm">
                                                <i class="bi bi-box-arrow-in-right"></i> Qo'shilish
                                            </a>
                                        @endif
                                        @if ($isTeacher && $session->status === 'scheduled')
                                            <form method="POST" action="{{ route('live-sessions.cancel', $session) }}">
                                                @csrf
                                                <button type="submit" class="btn-secondary btn-sm">Bekor qilish</button>
                                            </form>
                                        @endif
                                        @if ($isTeacher && $session->status === 'live')
                                            <form method="POST" action="{{ route('live-sessions.end', $session) }}"
                                                onsubmit="return confirm('Darsni hammaga yakunlaysizmi?');">
                                                @csrf
                                                <button type="submit" class="btn-danger-outline btn-sm">Yakunlash</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="group-col">
                @if ($isTeacher)
                    {{-- TAKLIF QILISH --}}
                    <div class="panel fade-up">
                        <div class="panel-head"><h2><i class="bi bi-person-plus-fill"></i> O'quvchi qo'shish</h2></div>

                        <label class="hint-label">Taklif havolasi</label>
                        <div class="copy-row">
                            <input type="text" readonly value="{{ $inviteLink }}" id="invite-link-input">
                            <button type="button" class="btn-secondary btn-sm" onclick="darsqilCopyInviteLink()">
                                <i class="bi bi-clipboard"></i> Nusxalash
                            </button>
                        </div>
                        <p class="hint-text">Bu havolani yuborgan har bir kishi (hisobi bo'lsa) guruhga qo'shiladi.</p>

                        <label class="hint-label" style="margin-top:16px;">Email orqali taklif</label>
                        <form method="POST" action="{{ route('groups.members.add', $group) }}" class="copy-row">
                            @csrf
                            <input type="email" name="email" placeholder="o'quvchi@email.com" required>
                            <button type="submit" class="btn-primary btn-sm"><i class="bi bi-send-fill"></i> Yuborish</button>
                        </form>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                {{-- A'ZOLAR --}}
                <div class="panel fade-up">
                    <div class="panel-head"><h2><i class="bi bi-people-fill"></i> A'zolar ({{ $group->members->count() }})</h2></div>
                    @if ($group->members->isEmpty())
                        <div class="empty-inline">Hali o'quvchi qo'shilmagan.</div>
                    @else
                        <div class="member-list">
                            @foreach ($group->members as $member)
                                <div class="member-item">
                                    <div class="member-avatar">{{ mb_substr($member->name, 0, 1) }}</div>
                                    <div class="member-info">
                                        <div class="member-name">{{ $member->name }}</div>
                                        <div class="member-email">{{ $member->email }}</div>
                                    </div>
                                    @if ($isTeacher)
                                        <form method="POST" action="{{ route('groups.members.remove', [$group, $member]) }}"
                                            onsubmit="return confirm('{{ $member->name }} guruhdan chiqarilsinmi?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn-sm" title="Chiqarish"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .group-grid-2 {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .group-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .group-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

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

        .session-form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 6px;
        }

        .session-form input[type="text"] {
            flex: 1 1 220px;
        }

        .session-form input,
        .copy-row input {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 9px 12px;
            font-size: .85rem;
            background: var(--bg);
            color: var(--text);
        }

        .hint-text {
            font-size: .76rem;
            color: var(--muted);
            margin: 4px 0 14px;
        }

        .hint-label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 6px;
        }

        .copy-row {
            display: flex;
            gap: 8px;
        }

        .copy-row input {
            flex: 1;
        }

        .btn-sm {
            padding: 8px 14px !important;
            font-size: .8rem !important;
        }

        .empty-inline {
            color: var(--muted);
            font-size: .85rem;
            padding: 10px 0;
        }

        .session-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
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
            animation: pulse-dot 1.4s infinite;
        }

        .status-scheduled {
            background: var(--amber);
        }

        .status-ended {
            background: var(--muted);
        }

        .status-canceled {
            background: var(--muted);
        }

        @keyframes pulse-dot {
            0% { box-shadow: 0 0 0 0 var(--coral-soft); }
            70% { box-shadow: 0 0 0 6px transparent; }
            100% { box-shadow: 0 0 0 0 transparent; }
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

        .session-item-actions {
            display: flex;
            gap: 8px;
        }

        .member-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .member-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid var(--line);
        }

        .member-item:last-child {
            border-bottom: none;
        }

        .member-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .member-info {
            flex: 1;
            min-width: 0;
        }

        .member-name {
            font-weight: 600;
            font-size: .85rem;
            color: var(--text);
        }

        .member-email {
            font-size: .76rem;
            color: var(--muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .icon-btn-sm {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-btn-sm:hover {
            background: var(--coral-soft);
            color: var(--coral);
            border-color: var(--coral-soft);
        }

        .btn-danger-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid var(--coral-soft);
            background: var(--coral-soft);
            color: var(--coral);
            font-weight: 700;
            font-size: .84rem;
        }

        .btn-danger-outline:hover {
            background: var(--coral);
            color: #fff;
        }

        .mine-alert-danger {
            background: var(--coral-soft);
            color: var(--coral);
        }
    </style>

    <script>
        function darsqilCopyInviteLink() {
            const input = document.getElementById('invite-link-input');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard?.writeText(input.value);
        }
    </script>
@endsection
