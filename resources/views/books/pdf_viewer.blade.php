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

        <div class="card" style="padding:12px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                <strong>{{ $bookFile->original_name }}</strong>
                <div style="margin-left:auto">
                    <button id="prevPage" class="btn-ghost">Oldingi</button>
                    <span id="pageLabel">1</span>
                    <button id="nextPage" class="btn-ghost">Keyingi</button>
                </div>
            </div>

            <div style="background:#fff;border-radius:8px;overflow:hidden;display:flex;align-items:center;justify-content:center;min-height:60vh;">
                <img id="viewerImage" src="" alt="PDF page" style="max-width:100%;height:auto;display:block;user-select:none;" />
            </div>
        </div>
    </div>

    <style>
        .back-link { display:inline-flex;align-items:center;gap:8px;color:var(--muted);font-weight:600;font-size:.85rem;margin-bottom:16px; }
    </style>

    <script>
        (function () {
            const book = @json($book->id);
            const fileId = @json($bookFile->id);
            const pagesUrl = '{{ route('books.pages', [$book, $bookFile]) }}';
            const pageUrlBase = '{{ url('') }}' + '/books/' + book + '/files/' + fileId + '/page/';

            let current = 1;
            let last = 1;

            const pageLabel = document.getElementById('pageLabel');
            const viewerImage = document.getElementById('viewerImage');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');

            function setLoading() {
                viewerImage.src = '';
                pageLabel.textContent = current + '/' + last;
            }

            function loadPage(n) {
                setLoading();
                const url = pageUrlBase + n;
                // add no-cache query to reduce chance of disk caching
                viewerImage.src = url + '?_=' + Date.now();
                viewerImage.onload = function () {
                    pageLabel.textContent = n + '/' + last;
                };
                viewerImage.onerror = function () {
                    // treat as missing page
                    pageLabel.textContent = '—';
                };
            }

            fetch(pagesUrl).then(r => r.json()).then(data => {
                last = data.pages || 1;
                pageLabel.textContent = current + '/' + last;
                loadPage(1);
            }).catch(() => {
                // fallback: try show page 1
                last = 1;
                loadPage(1);
            });

            prevBtn.addEventListener('click', function () {
                if (current > 1) {
                    current--; loadPage(current);
                }
            });
            nextBtn.addEventListener('click', function () {
                if (current < last) {
                    current++; loadPage(current);
                }
            });

            // Disable right click and common save/print while viewing
            document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
            window.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && ['s', 'p'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                    return false;
                }
            }, { passive: false });
        })();
    </script>
@endsection
