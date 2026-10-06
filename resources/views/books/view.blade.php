@php
    $viewer = auth()->user();
    $isTeacherReader = $viewer && ($viewer->hasRole('teacher') || $viewer->hasRole('admin'));
@endphp

@extends($isTeacherReader ? 'layouts.teacher' : 'layouts.student')

@section('content')
    <div class="page">
        <a href="{{ $isTeacherReader ? route('books.mine') : route('student-books.index') }}" class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> Orqaga
        </a>

        <div class="book-view-head fade-up">
            <h1>{{ $book->title }}</h1>
            @if ($book->description)
                <p class="book-view-desc">{{ $book->description }}</p>
            @endif

            @if (auth()->check() && auth()->id() === $book->user_id)
                <div style="margin-top:8px;display:flex;gap:8px;">
                    <a href="{{ route('books.edit', $book) }}" class="btn-ghost">Tahrirlash</a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST"
                        onsubmit="return confirm('Kitobni o\'chirmoqchimisiz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">O'chirish</button>
                    </form>
                </div>
            @endif
        </div>

        @foreach ($book->files as $file)
            <div class="pdf-wrap fade-up protected">
                <div class="pdf-wrap-head">
                    <i class="bi bi-file-earmark-pdf"></i> {{ $file->original_name }}
                    <div style="margin-left:auto">
                        <button type="button" class="btn-ghost" onclick="openFullScreen(this)">To'liq ekran</button>
                    </div>
                </div>
                <div class="pdf-frame-container" data-src="{{ route('books.stream', [$book, $file]) }}">
                    <div class="pdf-loading">Yuklanmoqda...</div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 16px;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .book-view-head {
            margin-bottom: 18px;
        }

        .book-view-head h1 {
            font-size: 1.4rem;
            margin: 0 0 6px;
        }

        .book-view-desc {
            color: var(--muted);
            font-size: .88rem;
            max-width: 700px;
            margin: 0;
        }

        .pdf-wrap {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .pdf-wrap-head {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
            font-weight: 600;
            font-size: .88rem;
            color: var(--text);
        }

        .pdf-wrap-head i {
            color: var(--coral);
        }

        .pdf-frame-container {
            max-height: 80vh;
            overflow-y: auto;
            background: #ececec;
            padding: 12px;
        }

        .pdf-frame-container:fullscreen {
            max-height: 100vh;
            height: 100vh;
        }

        .pdf-frame-container canvas {
            display: block;
            margin: 0 auto 12px;
            max-width: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
        }

        .pdf-loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .protected,
        .protected * {
            -webkit-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }

        .protected canvas {
            pointer-events: none;
        }

        @media (max-width:767px) {
            .pdf-frame-container {
                max-height: 65vh;
            }
        }

        @media print {
            body * {
                display: none !important;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        async function renderPdf(box) {
            try {
                const pdf = await pdfjsLib.getDocument({
                    url: box.dataset.src,
                    withCredentials: true
                }).promise;

                box.innerHTML = '';
                const width = Math.min(box.clientWidth - 24, 1000);
                const ratio = window.devicePixelRatio || 1;

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const base = page.getViewport({
                        scale: 1
                    });
                    const scale = width / base.width;
                    const viewport = page.getViewport({
                        scale: scale * ratio
                    });

                    const canvas = document.createElement('canvas');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    canvas.style.width = (viewport.width / ratio) + 'px';
                    box.appendChild(canvas);

                    await page.render({
                        canvasContext: canvas.getContext('2d'),
                        viewport
                    }).promise;
                }
            } catch (e) {
                console.error(e);
                box.innerHTML = '<div class="pdf-loading">Kitobni yuklab bo\'lmadi.</div>';
            }
        }

        document.querySelectorAll('.pdf-frame-container').forEach(renderPdf);

        function openFullScreen(btn) {
            const c = btn.closest('.pdf-wrap').querySelector('.pdf-frame-container');
            (c.requestFullscreen || c.webkitRequestFullscreen).call(c);
        }

        // O'ng tugma kitob ustida hamma uchun o'chirilgan
        document.addEventListener('contextmenu', e => {
            if (e.target.closest('.protected')) e.preventDefault();
        });

        // Saqlash, chop etish, manbani ko'rish, nusxalash
        window.addEventListener('keydown', e => {
            const k = e.key.toLowerCase();
            if ((e.ctrlKey || e.metaKey) && ['s', 'p', 'u', 'c'].includes(k)) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        document.addEventListener('dragstart', e => e.preventDefault());
    </script>
@endsection
