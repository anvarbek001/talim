@extends($book->user_id === auth()->id() ? 'layouts.teacher' : 'layouts.student')

@section('content')
    <div class="page">
        <a href="{{ $book->user_id === auth()->id() ? route('books.mine') : route('student-books.index') }}"
            class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> Orqaga
        </a>

        <div class="book-view-head fade-up">
            <h1>{{ $book->title }}</h1>
            @if ($book->description)
                <p class="book-view-desc">{{ $book->description }}</p>
            @endif

            @if(auth()->check() && auth()->id() === $book->user_id)
                <div style="margin-top:8px;display:flex;gap:8px;">
                    <a href="{{ route('books.edit', $book) }}" class="btn-ghost">Tahrirlash</a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Kitobni o\'chirmoqchimisiz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">O'chirish</button>
                    </form>
                </div>
            @endif
        </div>

        @foreach ($book->files as $file)
            <div class="pdf-wrap fade-up" oncontextmenu="return false;">
                <div class="pdf-wrap-head">
                    <i class="bi bi-file-earmark-pdf"></i> {{ $file->original_name }}
                    <div style="float:right">
                        <button class="btn-ghost" onclick="openFullScreen(this)">To'liq ekran</button>
                    </div>
                </div>
                {{-- Hide toolbar where possible; client-side deterrent only. --}}
                <div class="pdf-frame-container">
                    <iframe src="{{ route('books.stream', [$book, $file]) }}#toolbar=0&navpanes=0" class="pdf-frame"
                        title="{{ $file->original_name }}" sandbox="allow-same-origin allow-scripts"></iframe>
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

        .pdf-frame {
            width: 100%;
            height: 80vh;
            border: 0;
            display: block;
        }

        /* Students prefer full-screen reading — make the iframe fill viewport
           when requested via the Fullscreen API. */
        .pdf-frame.fullscreen {
            height: 100vh;
        }

        @media (max-width:767px) {
            .pdf-frame {
                height: 65vh;
            }
        }
    </style>
    <script>
        function openFullScreen(btn) {
            const container = btn.closest('.pdf-wrap').querySelector('.pdf-frame-container');
            const iframe = container.querySelector('.pdf-frame');
            // try Fullscreen API on container
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            }
            iframe.classList.add('fullscreen');
        }

        // Block common save/print shortcuts and context menu as a deterrent
        const isOwner = {{ auth()->check() && auth()->id() === $book->user_id ? 'true' : 'false' }};

        window.addEventListener('keydown', function (e) {
            // Ctrl/Cmd+S, Ctrl/Cmd+P, Ctrl+Shift+S
            if ((e.ctrlKey || e.metaKey) && ['s', 'p'].includes(e.key.toLowerCase())) {
                e.preventDefault();
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 's') {
                e.preventDefault();
                return false;
            }
        }, { passive: false });

        document.addEventListener('contextmenu', function (e) {
            // allow right-click for the owner/teacher to keep editing capabilities
            if (! isOwner && e.target.closest('.pdf-wrap')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
