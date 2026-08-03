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
        </div>

        @foreach ($book->files as $file)
            <div class="pdf-wrap fade-up">
                <div class="pdf-wrap-head">
                    <i class="bi bi-file-earmark-pdf"></i> {{ $file->original_name }}
                </div>
                {{--
                    "#toolbar=0&navpanes=0" hides Chromium's native PDF-viewer
                    toolbar (including its download button). This is a
                    Chromium-only convention — Firefox's built-in pdf.js
                    viewer.html does not honor it and will still show its own
                    toolbar with a download icon. It's a deterrent, not a
                    cross-browser guarantee that the file can't be saved.
                --}}
                <iframe src="{{ route('books.stream', [$book, $file]) }}#toolbar=0&navpanes=0" class="pdf-frame"
                    title="{{ $file->original_name }}"></iframe>
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

        @media (max-width:767px) {
            .pdf-frame {
                height: 65vh;
            }
        }
    </style>
@endsection
