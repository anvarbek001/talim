<!DOCTYPE html>
<html lang="uz" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $liveSession->title }} — DarsQil jonli dars</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>

    <style>
        :root {
            --navy: #141B33;
            --navy-deep: #0C1024;
            --gold: #F2A93B;
            --coral: #FF6B6B;
            --mint: #12B886;
            --panel: #1A2140;
            --line: #2A3157;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--navy-deep);
            color: #fff;
            overflow: hidden;
        }

        h1, h2, .display-font {
            font-family: 'Sora', sans-serif;
        }

        /* ---------- PRE-JOIN ---------- */
        #prejoin-screen {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 22px;
            padding: 24px;
        }

        .prejoin-card {
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .prejoin-preview {
            width: 100%;
            aspect-ratio: 16/10;
            background: #000;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            margin-bottom: 18px;
            border: 1px solid var(--line);
        }

        .prejoin-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .prejoin-preview .cam-off-msg {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            color: #7C84AE;
        }

        .device-toggles {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 18px;
        }

        .device-toggle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: var(--panel);
            color: #fff;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .device-toggle.is-off {
            background: var(--coral);
            border-color: var(--coral);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy-deep);
            font-weight: 700;
            border-radius: 12px;
            border: none;
            padding: 13px 26px;
            font-size: .95rem;
            width: 100%;
        }

        .btn-gold:disabled {
            opacity: .6;
        }

        .prejoin-sub {
            color: #9AA1C4;
            font-size: .85rem;
            margin-top: 12px;
        }

        /* ---------- CALL SCREEN ---------- */
        #call-screen {
            height: 100%;
            display: none;
            flex-direction: column;
        }

        .call-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            background: var(--panel);
            border-bottom: 1px solid var(--line);
            flex-shrink: 0;
        }

        .call-title {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .95rem;
        }

        .live-badge {
            background: var(--coral);
            font-size: .68rem;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 20px;
            letter-spacing: .04em;
        }

        .rec-badge {
            background: #fff;
            color: var(--coral);
            font-size: .68rem;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 20px;
            display: none;
            align-items: center;
            gap: 5px;
        }

        .rec-badge.is-active {
            display: inline-flex;
        }

        .rec-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--coral);
            animation: rec-blink 1s infinite;
        }

        @keyframes rec-blink {
            50% { opacity: .2; }
        }

        .call-timer {
            font-variant-numeric: tabular-nums;
            color: #9AA1C4;
            font-size: .85rem;
        }

        .call-close {
            color: #9AA1C4;
            font-size: 1.3rem;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .call-close:hover {
            background: var(--line);
            color: #fff;
        }

        .call-stage {
            flex: 1;
            min-height: 0;
            padding: 16px;
            display: flex;
        }

        .video-grid {
            flex: 1;
            display: grid;
            gap: 12px;
            align-content: center;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .tile {
            position: relative;
            background: #000;
            border-radius: 14px;
            overflow: hidden;
            aspect-ratio: 16/10;
            border: 1px solid var(--line);
        }

        .tile video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tile.is-local video {
            transform: scaleX(-1);
        }

        .tile-label {
            position: absolute;
            left: 10px;
            bottom: 8px;
            background: rgba(12, 16, 36, .72);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .76rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tile-label .bi-mic-mute-fill {
            color: var(--coral);
        }

        .tile-hand-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--gold);
            color: var(--navy-deep);
            display: none;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
        }

        .tile.hand-raised .tile-hand-badge {
            display: flex;
        }

        .tile-avatar {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #233065, #46579c);
            font-size: 1.6rem;
            font-weight: 800;
            font-family: 'Sora', sans-serif;
        }

        .call-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px;
            background: var(--panel);
            border-top: 1px solid var(--line);
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .ctrl-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: #232B52;
            color: #fff;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .ctrl-btn:hover {
            background: #2C3564;
        }

        .ctrl-btn.is-off {
            background: var(--coral);
            border-color: var(--coral);
        }

        .ctrl-btn.is-recording {
            background: var(--coral);
            border-color: var(--coral);
        }

        .ctrl-btn.is-raised {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--navy-deep);
        }

        .ctrl-btn.leave-btn {
            background: var(--coral);
            border-radius: 25px;
            width: auto;
            padding: 0 22px;
            font-weight: 700;
            font-size: .85rem;
            gap: 8px;
        }

        .ctrl-btn.end-btn {
            background: transparent;
            border: 1px solid var(--coral);
            color: var(--coral);
            border-radius: 25px;
            width: auto;
            padding: 0 18px;
            font-weight: 700;
            font-size: .82rem;
        }

        .ctrl-label {
            position: absolute;
            bottom: -20px;
            font-size: .62rem;
            color: #9AA1C4;
            white-space: nowrap;
        }

        /* ---------- PARTICIPANTS PANEL ---------- */
        .participants-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 280px;
            background: var(--panel);
            border-left: 1px solid var(--line);
            padding: 18px;
            overflow-y: auto;
            z-index: 20;
        }

        .participants-panel h3 {
            font-size: .95rem;
            margin-bottom: 14px;
        }

        .participant-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 0;
            font-size: .85rem;
        }

        .participant-row .p-info {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .participant-row .p-info span:last-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .participant-row .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--mint);
            flex-shrink: 0;
        }

        .participant-row .mic-icon.is-muted {
            color: var(--coral);
        }

        .participant-row .hand-icon {
            color: var(--gold);
        }

        .participant-row .p-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .participant-row .p-actions button {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: none;
            background: var(--line);
            color: #fff;
            font-size: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .participant-row .p-actions button.grant {
            background: var(--mint);
            color: #08331f;
        }

        .participant-row .p-actions button.mute-btn {
            background: var(--coral);
        }

        /* ---------- ERROR / TOAST ---------- */
        #error-screen {
            display: none;
            height: 100%;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 14px;
            text-align: center;
            padding: 24px;
        }

        #error-screen i {
            font-size: 2.4rem;
            color: var(--coral);
        }

        .toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #232B52;
            border: 1px solid var(--line);
            padding: 10px 18px;
            border-radius: 10px;
            font-size: .82rem;
            z-index: 30;
            opacity: 0;
            transition: opacity .3s;
            pointer-events: none;
        }

        .toast.is-visible {
            opacity: 1;
        }

        @media (max-width: 640px) {
            .participants-panel {
                width: 100%;
            }

            .ctrl-label {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div id="live-room-root"
        data-join-url="{{ route('live-sessions.join', $liveSession) }}"
        data-leave-url="{{ route('live-sessions.leave', $liveSession) }}"
        data-status-url="{{ route('live-sessions.status', $liveSession) }}"
        data-end-url="{{ $isModerator ? route('live-sessions.end', $liveSession) : '' }}"
        data-group-url="{{ route('groups.show', $group) }}"
        data-is-moderator="{{ $isModerator ? '1' : '0' }}"
        data-title="{{ $liveSession->title }}"
        data-display-name="{{ auth()->user()->name }}">

        {{-- PRE-JOIN --}}
        <div id="prejoin-screen">
            <div class="prejoin-card">
                <div class="prejoin-preview">
                    <video id="prejoin-video" autoplay muted playsinline></video>
                    <div class="cam-off-msg" id="prejoin-cam-off" style="display:none;">
                        <i class="bi bi-camera-video-off fs-2"></i>
                        <span>Kamera o'chirilgan</span>
                    </div>
                </div>
                <div class="device-toggles">
                    <button type="button" class="device-toggle" id="prejoin-mic-toggle" title="Mikrofon">
                        <i class="bi bi-mic-fill"></i>
                    </button>
                    <button type="button" class="device-toggle" id="prejoin-cam-toggle" title="Kamera">
                        <i class="bi bi-camera-video-fill"></i>
                    </button>
                </div>
                <h2 class="h5 mb-2">{{ $liveSession->title }}</h2>
                <button type="button" class="btn-gold" id="prejoin-join-btn">
                    <i class="bi bi-box-arrow-in-right"></i> Darsga qo'shilish
                </button>
                <div class="prejoin-sub" id="prejoin-status">Kameraga ruxsat so'ralmoqda...</div>
                @unless ($isModerator)
                    <div class="prejoin-sub">Darsga jim (mikrofon o'chiq) holda qo'shilasiz. So'z olish uchun
                        qo'lingizni ko'taring — domla ruxsat bersa, mikrofoningiz avtomatik yoqiladi.</div>
                @endunless
            </div>
        </div>

        {{-- CALL SCREEN --}}
        <div id="call-screen">
            <header class="call-topbar">
                <div class="call-title">
                    {{ $liveSession->title }}
                    <span class="live-badge">JONLI</span>
                    <span class="rec-badge" id="rec-badge"><span class="dot"></span> YOZILMOQDA <span id="rec-timer">00:00</span></span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="call-timer" id="call-timer">00:00</div>
                    <a href="{{ route('groups.show', $group) }}" class="call-close"><i class="bi bi-x-lg"></i></a>
                </div>
            </header>
            <main class="call-stage">
                <div class="video-grid" id="video-grid"></div>
            </main>
            <footer class="call-controls">
                <div style="position:relative;">
                    <button type="button" class="ctrl-btn" id="btn-mic"><i class="bi bi-mic-fill"></i></button>
                    <span class="ctrl-label">Mikrofon</span>
                </div>
                <div style="position:relative;">
                    <button type="button" class="ctrl-btn" id="btn-cam"><i class="bi bi-camera-video-fill"></i></button>
                    <span class="ctrl-label">Kamera</span>
                </div>
                <div style="position:relative;">
                    <button type="button" class="ctrl-btn" id="btn-share"><i class="bi bi-display"></i></button>
                    <span class="ctrl-label">Ekran</span>
                </div>
                @unless ($isModerator)
                    <div style="position:relative;">
                        <button type="button" class="ctrl-btn" id="btn-hand"><i class="bi bi-hand-index-thumb"></i></button>
                        <span class="ctrl-label">Qo'l ko'tarish</span>
                    </div>
                @endunless
                <div style="position:relative;">
                    <button type="button" class="ctrl-btn" id="btn-record"><i class="bi bi-record-circle"></i></button>
                    <span class="ctrl-label">Yozib olish</span>
                </div>
                <div style="position:relative;">
                    <button type="button" class="ctrl-btn" id="btn-participants"><i class="bi bi-people-fill"></i></button>
                    <span class="ctrl-label">Ishtirokchilar</span>
                </div>
                <button type="button" class="ctrl-btn leave-btn" id="btn-leave">
                    <i class="bi bi-telephone-x-fill"></i> Chiqish
                </button>
                @if ($isModerator)
                    <button type="button" class="ctrl-btn end-btn" id="btn-end">Darsni yakunlash</button>
                @endif
            </footer>

            <aside class="participants-panel" id="participants-panel" hidden>
                <h3><i class="bi bi-people-fill"></i> Ishtirokchilar (<span id="participants-count">1</span>)</h3>
                <div id="participants-list"></div>
            </aside>
        </div>

        {{-- ERROR SCREEN --}}
        <div id="error-screen">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div id="error-message" class="h6" style="max-width:420px;"></div>
            <a href="{{ route('groups.show', $group) }}" class="btn-gold" style="width:auto;">Guruhga qaytish</a>
        </div>

        <div class="toast" id="toast"></div>
    </div>

    <script src="{{ asset('js/live-room.js') }}"></script>
</body>

</html>
