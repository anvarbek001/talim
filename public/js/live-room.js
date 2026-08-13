/**
 * DarsQil — jonli dars xonasi (LiveKit orqali).
 *
 * - Video/audio: LiveKit JS SDK (CDN, global `LivekitClient`) — barcha
 *   ishtirokchilarning kamerasi bir xil yuqori tom (1080p) bilan yoqiladi,
 *   simulcast yordamida esa har kimning haqiqiy internet tezligiga qarab
 *   sifat avtomatik pasayadi/ko'tariladi (hech kim sun'iy ravishda pastroq
 *   sifatga qulflab qo'yilmaydi).
 * - Yozib olish: butunlay brauzer ichida (canvas + MediaRecorder), faqat
 *   shu qurilmada — hech qanday narsa serverga yuborilmaydi va boshqa
 *   ishtirokchilarning internet tezligiga ta'sir qilmaydi, shuning uchun
 *   1440p (2560x1440) kabi yuqoriroq sifatda yoziladi, tugagach fayl
 *   foydalanuvchining o'z kompyuteriga avtomatik yuklab olinadi.
 */
(function () {
    'use strict';

    const root = document.getElementById('live-room-root');
    if (!root) return;

    const cfg = {
        joinUrl: root.dataset.joinUrl,
        leaveUrl: root.dataset.leaveUrl,
        statusUrl: root.dataset.statusUrl,
        endUrl: root.dataset.endUrl,
        groupUrl: root.dataset.groupUrl,
        isModerator: root.dataset.isModerator === '1',
        title: root.dataset.title,
        displayName: root.dataset.displayName,
        csrf: document.querySelector('meta[name="csrf-token"]').content,
        identity: null, // join javobidan keyin to'ldiriladi
    };

    const $ = (id) => document.getElementById(id);
    const prejoinScreen = $('prejoin-screen');
    const callScreen = $('call-screen');
    const errorScreen = $('error-screen');
    const videoGrid = $('video-grid');
    const thumbsEl = $('stage-thumbs');
    const toastEl = $('toast');

    // Bosilgan katakning id'si — shu kishi/ekran "asosiy ekran" (spotlight)
    // sifatida katta ko'rsatiladi, qolganlar pastda kichik qatorda turadi.
    let pinnedTileId = null;

    let room = null;
    let previewStream = null;
    let micEnabled = true;
    let camEnabled = true;
    let screenShareEnabled = false;
    let handRaised = false;

    // Masofadagi ishtirokchilarning mikrofon/qo'l holati — panel va
    // video kataklaridagi belgilarni chizish uchun (Meet uslubidagi kuzatuv).
    const remoteMicMuted = new Map();
    const remoteHandRaised = new Map();

    let recording = false;
    let mediaRecorder = null;
    let recordedChunks = [];
    let recordAudioCtx = null;
    let recordStartedAt = null;
    let recTimerInterval = null;

    let callStartedAt = null;
    let callTimerInterval = null;
    let statusPollTimer = null;

    function showToast(msg) {
        toastEl.textContent = msg;
        toastEl.classList.add('is-visible');
        setTimeout(() => toastEl.classList.remove('is-visible'), 3000);
    }

    function showError(message) {
        prejoinScreen.style.display = 'none';
        callScreen.style.display = 'none';
        errorScreen.style.display = 'flex';
        $('error-message').textContent = message;
    }

    function formatTime(totalSeconds) {
        const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const s = Math.floor(totalSeconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    async function postJson(url) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': cfg.csrf,
                Accept: 'application/json',
            },
        });
    }

    // ==================== PRE-JOIN ====================

    async function initPrejoin() {
        try {
            previewStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            $('prejoin-video').srcObject = previewStream;
            $('prejoin-status').textContent = "Tayyor bo'lganingizda qo'shiling.";
        } catch (e) {
            camEnabled = false;
            micEnabled = false;
            $('prejoin-cam-off').style.display = 'flex';
            $('prejoin-status').textContent = "Kamera/mikrofonga ruxsat berilmadi. Ruxsatsiz ham qo'shilishingiz mumkin.";
            updatePrejoinToggleUI();
        }
    }

    function updatePrejoinToggleUI() {
        $('prejoin-mic-toggle').classList.toggle('is-off', !micEnabled);
        $('prejoin-mic-toggle').innerHTML = `<i class="bi ${micEnabled ? 'bi-mic-fill' : 'bi-mic-mute-fill'}"></i>`;
        $('prejoin-cam-toggle').classList.toggle('is-off', !camEnabled);
        $('prejoin-cam-toggle').innerHTML = `<i class="bi ${camEnabled ? 'bi-camera-video-fill' : 'bi-camera-video-off-fill'}"></i>`;
        $('prejoin-cam-off').style.display = camEnabled ? 'none' : 'flex';
    }

    $('prejoin-mic-toggle')?.addEventListener('click', () => {
        micEnabled = !micEnabled;
        previewStream?.getAudioTracks().forEach((t) => (t.enabled = micEnabled));
        updatePrejoinToggleUI();
    });

    $('prejoin-cam-toggle')?.addEventListener('click', () => {
        camEnabled = !camEnabled;
        previewStream?.getVideoTracks().forEach((t) => (t.enabled = camEnabled));
        updatePrejoinToggleUI();
    });

    $('prejoin-join-btn')?.addEventListener('click', joinCall);

    // ==================== JOIN CALL ====================

    async function joinCall() {
        $('prejoin-join-btn').disabled = true;
        $('prejoin-status').textContent = 'Ulanmoqda...';

        let data;
        try {
            const res = await postJson(cfg.joinUrl);
            if (!res.ok) {
                const body = await res.json().catch(() => ({}));
                throw new Error(body.message || "Xonaga ulanib bo'lmadi.");
            }
            data = await res.json();
        } catch (e) {
            showError(e.message || "Xonaga ulanib bo'lmadi.");
            return;
        }

        cfg.identity = data.identity;

        // Google Meet uslubidagi "so'z berish" modeli: o'quvchilar darsga
        // sukut bo'yicha jim (mikrofon o'chiq) holda qo'shiladi — domla
        // qo'l ko'targanlarga so'z bergach mikrofon avtomatik yoqiladi.
        if (!cfg.isModerator) {
            micEnabled = false;
        }

        if (typeof LivekitClient === 'undefined') {
            showError('Video kutubxonasi yuklanmadi. Internet aloqasini tekshirib, sahifani qayta yuklang.');
            return;
        }

        // Hammaga bir xil yuqori tom (1080p) — kimningdir sun'iy ravishda
        // pastroq sifatga qulflab qo'yilishiga hojat yo'q: simulcast yoqilgan
        // bo'lsa, LiveKit har bir kamerani bir nechta sifat qatlamida
        // (past/o'rta/yuqori) bir vaqtda uzatadi va har bir tomoshabinga
        // o'zining haqiqiy internet tezligiga mos qatlamni avtomatik
        // tanlaydi — internet tez bo'lsa sifat ham o'zi ko'tarilaveradi,
        // sekin bo'lsa faqat o'sha kishi uchun pasayadi (boshqalarga
        // ta'sir qilmaydi).
        room = new LivekitClient.Room({
            adaptiveStream: true,
            dynacast: true,
            publishDefaults: {
                simulcast: true,
            },
            videoCaptureDefaults: {
                resolution: LivekitClient.VideoPresets.h1080.resolution,
            },
        });

        registerRoomEvents(room);

        try {
            await room.connect(data.wsUrl, data.token);
        } catch (e) {
            showError("Xonaga ulanib bo'lmadi: " + (e?.message || e));
            return;
        }

        stopPreviewStream();
        addLocalTile(data.identity);

        try {
            await room.localParticipant.setMicrophoneEnabled(micEnabled);
        } catch (e) { /* ruxsat berilmagan bo'lishi mumkin */ }
        try {
            await room.localParticipant.setCameraEnabled(camEnabled);
        } catch (e) { /* ruxsat berilmagan bo'lishi mumkin */ }

        prejoinScreen.style.display = 'none';
        callScreen.style.display = 'flex';

        callStartedAt = Date.now();
        callTimerInterval = setInterval(updateCallTimer, 1000);
        statusPollTimer = setInterval(pollStatus, 8000);

        updateControlButtonsUI();
        updateParticipantsPanel();

        window.addEventListener('beforeunload', onBeforeUnload);
    }

    function stopPreviewStream() {
        previewStream?.getTracks().forEach((t) => t.stop());
        previewStream = null;
    }

    // ==================== TILES ====================

    function tileIdFor(identity) {
        return 'tile-' + identity.replace(/[^a-zA-Z0-9_-]/g, '');
    }

    // Kataklarni joylashtiradi: agar ekran ulashish ketayotgan bo'lsa yoki
    // kimdir bosib "asosiy ekran" qilib belgilagan bo'lsa — o'sha katak
    // katta (spotlight), qolganlari pastda kichik qatorda turadi. Aks holda
    // hammasi teng o'lchamdagi katakchalar tarzida (ishtirokchilar soniga
    // qarab avtomatik ustun/qator bilan) ko'rsatiladi.
    function layoutStage() {
        const tiles = [...videoGrid.children, ...thumbsEl.children];
        if (tiles.length === 0) return;

        if (pinnedTileId && !tiles.some((t) => t.id === pinnedTileId)) pinnedTileId = null;

        let mainTile = pinnedTileId ? tiles.find((t) => t.id === pinnedTileId) : null;
        if (!mainTile) mainTile = tiles.find((t) => t.dataset.screen === '1');
        if (!mainTile && tiles.length === 1) mainTile = tiles[0];

        if (!mainTile) {
            videoGrid.classList.remove('spotlight-mode');
            thumbsEl.hidden = true;
            thumbsEl.innerHTML = '';
            tiles.forEach((t) => {
                t.classList.remove('is-main');
                videoGrid.appendChild(t);
            });
            const n = tiles.length;
            const cols = Math.ceil(Math.sqrt(n));
            const rows = Math.ceil(n / cols);
            videoGrid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
            videoGrid.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
            return;
        }

        videoGrid.classList.add('spotlight-mode');
        videoGrid.style.gridTemplateColumns = '';
        videoGrid.style.gridTemplateRows = '';
        mainTile.classList.add('is-main');
        videoGrid.appendChild(mainTile);
        thumbsEl.hidden = tiles.length <= 1;
        tiles.forEach((t) => {
            if (t !== mainTile) {
                t.classList.remove('is-main');
                thumbsEl.appendChild(t);
            }
        });
    }

    function ensureTile(identity, name, isLocal, isScreen) {
        let tile = document.getElementById(tileIdFor(identity));
        if (tile) return tile;

        tile = document.createElement('div');
        tile.className = 'tile' + (isLocal ? ' is-local' : '') + (isScreen ? ' is-screen' : '');
        tile.id = tileIdFor(identity);
        tile.dataset.identity = identity;
        tile.dataset.local = isLocal ? '1' : '0';
        tile.dataset.screen = isScreen ? '1' : '0';
        tile.innerHTML = isScreen
            ? `<div class="tile-label"><i class="bi bi-display"></i> <span class="tile-name">${escapeHtml(name || 'Ekran')}</span></div>`
            : `
                <div class="tile-avatar">${(name || '?').substring(0, 1).toUpperCase()}</div>
                <div class="tile-hand-badge"><i class="bi bi-hand-index-thumb-fill"></i></div>
                <div class="tile-label">
                    <i class="bi bi-mic-mute-fill mic-status-icon"></i>
                    <span class="tile-name">${escapeHtml(name || 'Foydalanuvchi')}</span>
                </div>
            `;
        // Faqat domla asosiy ekranni belgilay oladi — va bu tanlov hammaga
        // (barcha ishtirokchilarga) yuboriladi, shaxsiy/lokal tanlov emas.
        if (cfg.isModerator) {
            tile.title = "Hammaga asosiy ekran qilib ko'rsatish uchun bosing";
            tile.style.cursor = 'pointer';
            tile.addEventListener('click', () => {
                const nowPinned = pinnedTileId !== tile.id;
                pinnedTileId = nowPinned ? tile.id : null;
                sendData({ type: 'spotlight', identity: nowPinned ? identity : null });
                layoutStage();
            });
        }
        videoGrid.appendChild(tile);
        layoutStage();
        return tile;
    }

    function updateMicIcon(identity, muted) {
        remoteMicMuted.set(identity, muted);
        const tile = document.getElementById(tileIdFor(identity));
        const icon = tile?.querySelector('.mic-status-icon');
        if (icon) icon.className = 'bi mic-status-icon ' + (muted ? 'bi-mic-mute-fill' : 'bi-mic-fill');
        updateParticipantsPanel();
    }

    function updateHandBadge(identity, raised) {
        remoteHandRaised.set(identity, raised);
        document.getElementById(tileIdFor(identity))?.classList.toggle('hand-raised', raised);
        updateParticipantsPanel();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function addLocalTile(identity) {
        ensureTile(identity, cfg.displayName + ' (Siz)', true);
    }

    function attachTrack(track, participant) {
        const baseIdentity = participant.identity;
        const isLocal = participant.isLocal;
        const isScreenShare = track.source === LivekitClient.Track.Source.ScreenShare;
        const baseName = isLocal ? cfg.displayName : (participant.name || participant.identity);

        // Ekran ulashish — kamera bilan bitta katakni bo'lishmaydi, aks
        // holda ekran tugagach kamera videosi qaytmay qolib qolardi.
        const identity = isScreenShare ? baseIdentity + '-screen-share' : baseIdentity;
        const name = isScreenShare ? baseName + ' — ekran' : (isLocal ? baseName + ' (Siz)' : baseName);
        const tile = ensureTile(identity, name, isLocal, isScreenShare);

        if (track.kind === 'video') {
            const el = track.attach();
            const avatar = tile.querySelector('.tile-avatar');
            if (avatar) avatar.remove();
            tile.querySelector('video')?.remove();
            tile.insertBefore(el, tile.firstChild);
        } else if (track.kind === 'audio') {
            if (!isLocal) {
                // Local mikrofonni ekranga chiqarmaymiz — aks holda o'z ovozini eshitadi (eho).
                tile.querySelector('audio')?.remove();
                const el = track.attach();
                tile.appendChild(el);
            }
            if (!isScreenShare) updateMicIcon(baseIdentity, track.isMuted);
        }

        updateParticipantsPanel();
    }

    function detachTrack(track) {
        track.detach().forEach((el) => el.remove());
    }

    function removeParticipantTile(identity) {
        document.getElementById(tileIdFor(identity))?.remove();
        removeScreenTile(identity);
    }

    function removeScreenTile(identity) {
        document.getElementById(tileIdFor(identity + '-screen-share'))?.remove();
        layoutStage();
        updateParticipantsPanel();
    }

    // ==================== ROOM EVENTS ====================

    function registerRoomEvents(room) {
        const RoomEvent = LivekitClient.RoomEvent;

        room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => attachTrack(track, participant));
        room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
            detachTrack(track);
            if (publication.source === LivekitClient.Track.Source.ScreenShare) removeScreenTile(participant.identity);
        });
        room.on(RoomEvent.LocalTrackPublished, (publication, participant) => {
            if (publication.track) attachTrack(publication.track, participant);
        });
        room.on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
            if (publication.track) detachTrack(publication.track);
            if (publication.source === LivekitClient.Track.Source.ScreenShare) removeScreenTile(participant.identity);
        });
        room.on(RoomEvent.ParticipantConnected, (participant) => {
            ensureTile(participant.identity, participant.name || participant.identity, false);
            updateParticipantsPanel();
            showToast((participant.name || 'Ishtirokchi') + " qo'shildi");
        });
        room.on(RoomEvent.ParticipantDisconnected, (participant) => removeParticipantTile(participant.identity));
        room.on(RoomEvent.Disconnected, () => {
            clearInterval(statusPollTimer);
            clearInterval(callTimerInterval);
        });

        room.on(RoomEvent.TrackMuted, (pub, participant) => {
            if (pub.source === LivekitClient.Track.Source.Microphone) updateMicIcon(participant.identity, true);
        });
        room.on(RoomEvent.TrackUnmuted, (pub, participant) => {
            if (pub.source === LivekitClient.Track.Source.Microphone) updateMicIcon(participant.identity, false);
        });

        room.on(RoomEvent.DataReceived, (payload) => {
            let msg;
            try {
                msg = JSON.parse(new TextDecoder().decode(payload));
            } catch (e) {
                return;
            }
            handleDataMessage(msg);
        });
    }

    // ==================== SO'Z BERISH / QO'L KO'TARISH ====================

    function sendData(payload) {
        if (!room) return;
        const data = new TextEncoder().encode(JSON.stringify(payload));
        try {
            room.localParticipant.publishData(data, { reliable: true });
        } catch (e) { /* eski SDK imzosi */ }
    }

    function handleDataMessage(msg) {
        if (msg.type === 'hand') {
            updateHandBadge(msg.from, !!msg.raised);
            if (msg.raised) showToast((msg.name || 'Ishtirokchi') + " qo'lini ko'tardi");
            return;
        }
        if (msg.type === 'spotlight') {
            // Domla hammaga bittasini "asosiy ekran" qilib belgiladi (yoki
            // bekor qildi) — bu tanlov barcha ishtirokchilar uchun majburiy.
            pinnedTileId = msg.identity ? tileIdFor(msg.identity) : null;
            layoutStage();
            return;
        }
        if (msg.to !== cfg.identity) return;

        if (msg.type === 'grant') {
            handRaised = false;
            updateHandBadge(cfg.identity, false);
            micEnabled = true;
            room?.localParticipant.setMicrophoneEnabled(true).catch(() => {});
            updateControlButtonsUI();
            updateHandButtonUI();
            showToast("Sizga so'z berildi — mikrofoningiz yoqildi.");
        } else if (msg.type === 'mute') {
            micEnabled = false;
            room?.localParticipant.setMicrophoneEnabled(false).catch(() => {});
            updateControlButtonsUI();
            showToast('Moderator mikrofoningizni o\'chirdi.');
        }
    }

    $('btn-hand')?.addEventListener('click', () => {
        handRaised = !handRaised;
        sendData({ type: 'hand', from: cfg.identity, name: cfg.displayName, raised: handRaised });
        updateHandBadge(cfg.identity, handRaised);
        updateHandButtonUI();
    });

    function updateHandButtonUI() {
        const btn = $('btn-hand');
        if (!btn) return;
        btn.classList.toggle('is-raised', handRaised);
        btn.innerHTML = `<i class="bi ${handRaised ? 'bi-hand-index-thumb-fill' : 'bi-hand-index-thumb'}"></i>`;
    }

    function grantFloor(identity, name) {
        sendData({ type: 'grant', to: identity });
        updateHandBadge(identity, false);
        showToast((name || 'Ishtirokchi') + " ga so'z berildi.");
    }

    function forceMute(identity, name) {
        sendData({ type: 'mute', to: identity });
        showToast((name || 'Ishtirokchi') + " ovozi o'chirilmoqda.");
    }

    // ==================== CONTROLS ====================

    function updateControlButtonsUI() {
        $('btn-mic').classList.toggle('is-off', !micEnabled);
        $('btn-mic').innerHTML = `<i class="bi ${micEnabled ? 'bi-mic-fill' : 'bi-mic-mute-fill'}"></i>`;
        $('btn-cam').classList.toggle('is-off', !camEnabled);
        $('btn-cam').innerHTML = `<i class="bi ${camEnabled ? 'bi-camera-video-fill' : 'bi-camera-video-off-fill'}"></i>`;
        $('btn-share').classList.toggle('is-off', screenShareEnabled);
        $('btn-record').classList.toggle('is-recording', recording);
    }

    $('btn-mic')?.addEventListener('click', async () => {
        micEnabled = !micEnabled;
        await room?.localParticipant.setMicrophoneEnabled(micEnabled);
        updateControlButtonsUI();
    });

    $('btn-cam')?.addEventListener('click', async () => {
        camEnabled = !camEnabled;
        await room?.localParticipant.setCameraEnabled(camEnabled);
        updateControlButtonsUI();
    });

    $('btn-share')?.addEventListener('click', async () => {
        screenShareEnabled = !screenShareEnabled;
        try {
            await room?.localParticipant.setScreenShareEnabled(screenShareEnabled);
        } catch (e) {
            screenShareEnabled = false;
        }
        updateControlButtonsUI();
    });

    $('btn-participants')?.addEventListener('click', () => {
        const panel = $('participants-panel');
        panel.hidden = !panel.hidden;
    });

    $('btn-leave')?.addEventListener('click', () => leaveCall(cfg.groupUrl));

    if (cfg.isModerator) {
        $('btn-end')?.addEventListener('click', async () => {
            if (!confirm('Darsni hammaga yakunlaysizmi?')) return;
            await stopRecordingIfActive();
            await postJson(cfg.endUrl);
            await leaveCall(cfg.groupUrl, true);
        });
    }

    async function leaveCall(redirectTo, skipRecordStop) {
        if (!skipRecordStop) await stopRecordingIfActive();
        clearInterval(statusPollTimer);
        clearInterval(callTimerInterval);
        try { await postJson(cfg.leaveUrl); } catch (e) {}
        try { room?.disconnect(); } catch (e) {}
        window.location.href = redirectTo;
    }

    function onBeforeUnload() {
        try {
            const blob = new Blob([JSON.stringify({ _token: cfg.csrf })], { type: 'application/json' });
            navigator.sendBeacon(cfg.leaveUrl, blob);
        } catch (e) {}
    }

    // ==================== STATUS POLLING ====================

    async function pollStatus() {
        try {
            const res = await fetch(cfg.statusUrl, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            if (data.status === 'ended' || data.status === 'canceled') {
                showToast("Dars yakunlandi. Bir necha soniyadan so'ng guruh sahifasiga qaytasiz...");
                await stopRecordingIfActive();
                setTimeout(() => leaveCall(cfg.groupUrl, true), 3000);
            }
        } catch (e) {}
    }

    function updateCallTimer() {
        $('call-timer').textContent = formatTime(Math.floor((Date.now() - callStartedAt) / 1000));
    }

    // ==================== PARTICIPANTS PANEL ====================

    function updateParticipantsPanel() {
        const tiles = [...videoGrid.querySelectorAll('.tile:not(.is-screen)'), ...thumbsEl.querySelectorAll('.tile:not(.is-screen)')];
        $('participants-count').textContent = tiles.length;
        const list = $('participants-list');
        list.innerHTML = '';
        tiles.forEach((tile) => {
            const identity = tile.dataset.identity;
            const isLocal = tile.dataset.local === '1';
            const name = tile.querySelector('.tile-name')?.textContent || tile.querySelector('.tile-avatar')?.textContent || 'Foydalanuvchi';
            const muted = isLocal ? !micEnabled : !!remoteMicMuted.get(identity);
            const raised = !!remoteHandRaised.get(identity);

            const row = document.createElement('div');
            row.className = 'participant-row';

            const info = document.createElement('div');
            info.className = 'p-info';
            info.innerHTML = `
                <span class="dot"></span>
                <i class="bi ${muted ? 'bi-mic-mute-fill' : 'bi-mic-fill'} mic-icon${muted ? ' is-muted' : ''}"></i>
                ${raised ? '<i class="bi bi-hand-index-thumb-fill hand-icon"></i>' : ''}
                <span>${escapeHtml(name)}</span>
            `;
            row.appendChild(info);

            if (cfg.isModerator && !isLocal) {
                const actions = document.createElement('div');
                actions.className = 'p-actions';
                const btn = document.createElement('button');
                if (muted) {
                    btn.className = 'grant';
                    btn.title = "So'z berish";
                    btn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                    btn.addEventListener('click', () => grantFloor(identity, name));
                } else {
                    btn.className = 'mute-btn';
                    btn.title = "Ovozini o'chirish";
                    btn.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
                    btn.addEventListener('click', () => forceMute(identity, name));
                }
                actions.appendChild(btn);
                row.appendChild(actions);
            }

            list.appendChild(row);
        });
    }

    // ==================== LOCAL RECORDING (>=720p, faqat shu qurilmada) ====================

    function pickMimeType() {
        const candidates = ['video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm'];
        return candidates.find((type) => window.MediaRecorder && MediaRecorder.isTypeSupported(type)) || '';
    }

    $('btn-record')?.addEventListener('click', () => {
        if (recording) {
            stopRecordingIfActive();
        } else {
            startRecording();
        }
    });

    function startRecording() {
        if (!window.MediaRecorder) {
            showToast("Brauzeringiz yozib olishni qo'llab-quvvatlamaydi.");
            return;
        }

        // Yozib olish faqat shu qurilmada (odatda domlaniki) ishlaydi va
        // hech kimning internet tezligiga ta'sir qilmaydi — shuning uchun
        // kamera olish sifatidan farqli o'laroq buni yuqoriroq qo'yish
        // xavfsiz (1440p).
        const RECORD_WIDTH = 2560;
        const RECORD_HEIGHT = 1440;

        const canvas = document.createElement('canvas');
        canvas.width = RECORD_WIDTH;
        canvas.height = RECORD_HEIGHT;
        const ctx = canvas.getContext('2d');

        recordAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const audioDest = recordAudioCtx.createMediaStreamDestination();
        const connectedTracks = new WeakSet();

        function connectTrackToMix(mediaStreamTrack) {
            if (!mediaStreamTrack || connectedTracks.has(mediaStreamTrack)) return;
            try {
                const src = recordAudioCtx.createMediaStreamSource(new MediaStream([mediaStreamTrack]));
                src.connect(audioDest);
                connectedTracks.add(mediaStreamTrack);
            } catch (e) { /* ba'zi treklarni ulab bo'lmasligi mumkin */ }
        }

        function connectAllAudio() {
            // Ekrandagi (masalan boshqa ishtirokchilar) audio/video elementlari orqali.
            document.querySelectorAll('#video-grid audio, #video-grid video, #stage-thumbs audio, #stage-thumbs video').forEach((el) => {
                el.srcObject?.getAudioTracks().forEach(connectTrackToMix);
            });
            // O'zining mikrofoni ekranga chiqarilmagan (eho bo'lmasligi uchun),
            // shuning uchun uni alohida qo'shamiz.
            room?.localParticipant?.trackPublications?.forEach((pub) => {
                if (pub.kind === 'audio' && pub.track?.mediaStreamTrack) {
                    connectTrackToMix(pub.track.mediaStreamTrack);
                }
            });
        }

        connectAllAudio();
        const audioRefreshInterval = setInterval(connectAllAudio, 3000);

        let drawing = true;

        function drawFrame() {
            if (!drawing) return;
            const videos = Array.from(document.querySelectorAll('#video-grid video, #stage-thumbs video')).filter((v) => v.readyState >= 2);
            ctx.fillStyle = '#0C1024';
            ctx.fillRect(0, 0, RECORD_WIDTH, RECORD_HEIGHT);

            const n = videos.length || 1;
            const cols = Math.ceil(Math.sqrt(n));
            const rows = Math.ceil(n / cols);
            const tileW = RECORD_WIDTH / cols;
            const tileH = RECORD_HEIGHT / rows;

            videos.forEach((video, i) => {
                const col = i % cols;
                const row = Math.floor(i / cols);
                const x = col * tileW;
                const y = row * tileH;

                const vRatio = video.videoWidth / video.videoHeight || 16 / 9;
                const tRatio = tileW / tileH;
                let sx = 0, sy = 0, sw = video.videoWidth, sh = video.videoHeight;
                if (vRatio > tRatio) {
                    sw = video.videoHeight * tRatio;
                    sx = (video.videoWidth - sw) / 2;
                } else {
                    sh = video.videoWidth / tRatio;
                    sy = (video.videoHeight - sh) / 2;
                }
                try {
                    ctx.drawImage(video, sx, sy, sw, sh, x, y, tileW, tileH);
                } catch (e) { /* freym hali tayyor bo'lmasligi mumkin */ }
            });

            requestAnimationFrame(drawFrame);
        }
        drawFrame();

        const canvasStream = canvas.captureStream(30);
        const combinedStream = new MediaStream([...canvasStream.getVideoTracks(), ...audioDest.stream.getAudioTracks()]);

        const mimeType = pickMimeType();
        recordedChunks = [];
        try {
            mediaRecorder = new MediaRecorder(combinedStream, {
                mimeType: mimeType || undefined,
                videoBitsPerSecond: 9_000_000,
            });
        } catch (e) {
            showToast("Yozib olishni boshlab bo'lmadi.");
            drawing = false;
            clearInterval(audioRefreshInterval);
            return;
        }

        mediaRecorder.ondataavailable = (e) => {
            if (e.data && e.data.size > 0) recordedChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            drawing = false;
            clearInterval(audioRefreshInterval);
            recordAudioCtx?.close().catch(() => {});

            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const safeTitle = (cfg.title || 'dars').toLowerCase().replace(/[^a-z0-9Ѐ-ӿʻʼ]+/gi, '-').replace(/^-+|-+$/g, '');
            const dateStr = new Date().toISOString().slice(0, 10);
            a.href = url;
            a.download = `darsqil-${safeTitle}-${dateStr}.webm`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(() => URL.revokeObjectURL(url), 5000);
            showToast('Yozuv kompyuteringizga yuklab olindi.');
        };

        mediaRecorder.start(1000);
        recording = true;
        recordStartedAt = Date.now();
        $('rec-badge').classList.add('is-active');
        recTimerInterval = setInterval(() => {
            $('rec-timer').textContent = formatTime(Math.floor((Date.now() - recordStartedAt) / 1000));
        }, 1000);
        updateControlButtonsUI();
        showToast('Yozib olish boshlandi (1440p). Tugagach fayl avtomatik yuklab olinadi.');
    }

    function stopRecordingIfActive() {
        if (!recording || !mediaRecorder) return Promise.resolve();
        recording = false;
        clearInterval(recTimerInterval);
        $('rec-badge').classList.remove('is-active');
        updateControlButtonsUI();
        return new Promise((resolve) => {
            mediaRecorder.addEventListener('stop', resolve, { once: true });
            mediaRecorder.stop();
        });
    }

    // ==================== INIT ====================

    initPrejoin();
})();
