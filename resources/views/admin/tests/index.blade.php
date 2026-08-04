@extends('layouts.admin')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Testlar</h1>
                <p class="page-sub">Barcha o'qituvchilarning mavzu, DTM va sertifikat testlarini ko'rish va o'chirish.</p>
            </div>
            <a href="{{ route('tests.index') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Test qo'shish</a>
        </div>

        @if (session('success'))
            <div class="empty-hint" style="background:var(--mint-soft);color:var(--mint);border-color:var(--mint);margin-bottom:16px;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="empty-hint" style="margin-bottom:16px;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.tests.index') }}" class="filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Test nomi bo'yicha qidirish..." class="filter-input">
            <select name="teacher" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha o'qituvchilar</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ (string) request('teacher') === (string) $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q') || request('teacher'))
                <a href="{{ route('admin.tests.index') }}" class="filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.05s;">
            <div class="card-head">
                <div class="card-title">Mavzu testlari</div>
                <div class="card-sub-text">{{ $topicTests->total() }} ta</div>
            </div>
            @if ($topicTests->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hech qanday mavzu testi topilmadi.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomi</th>
                                <th>O'qituvchi</th>
                                <th>Mavzu</th>
                                <th>Davomiyligi</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topicTests as $test)
                                <tr>
                                    <td class="cell-strong" data-label="Nomi">{{ $test->title }}</td>
                                    <td data-label="O'qituvchi">{{ $test->user->name }}</td>
                                    <td class="cell-muted" data-label="Mavzu">{{ $test->topic?->title }}</td>
                                    <td data-label="Davomiyligi">{{ $test->duration_minutes }} daqiqa</td>
                                    <td class="actions-cell">
                                        <form action="{{ route('admin.tests.topic.destroy', $test) }}" method="POST"
                                            onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $topicTests->links() }}
            @endif
        </div>

        <div class="card fade-up" style="animation-delay:.08s;">
            <div class="card-head">
                <div class="card-title">DTM testlari</div>
                <div class="card-sub-text">{{ $dtmTests->total() }} ta</div>
            </div>
            @if ($dtmTests->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hali DTM testi yaratilmagan.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomi</th>
                                <th>O'qituvchi</th>
                                <th>1-blok</th>
                                <th>2-blok</th>
                                <th>Davomiyligi</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dtmTests as $test)
                                <tr>
                                    <td class="cell-strong" data-label="Nomi">{{ $test->title }}</td>
                                    <td data-label="O'qituvchi">{{ $test->user->name }}</td>
                                    <td class="cell-muted" data-label="1-blok">{{ $test->block1Science?->title }}</td>
                                    <td class="cell-muted" data-label="2-blok">{{ $test->block2Science?->title }}</td>
                                    <td data-label="Davomiyligi">{{ $test->duration_minutes }} daqiqa</td>
                                    <td class="actions-cell">
                                        <form action="{{ route('admin.tests.dtm.destroy', $test) }}" method="POST"
                                            onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $dtmTests->links() }}
            @endif
        </div>

        <div class="card fade-up" style="animation-delay:.11s;">
            <div class="card-head">
                <div class="card-title">Sertifikat testlari</div>
                <div class="card-sub-text">{{ $sertifikatTests->total() }} ta</div>
            </div>
            @if ($sertifikatTests->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hali sertifikat testi yaratilmagan.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomi</th>
                                <th>O'qituvchi</th>
                                <th>Fan</th>
                                <th>Daraja</th>
                                <th>Davomiyligi</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sertifikatTests as $test)
                                <tr>
                                    <td class="cell-strong" data-label="Nomi">{{ $test->title }}</td>
                                    <td data-label="O'qituvchi">{{ $test->user->name }}</td>
                                    <td class="cell-muted" data-label="Fan">{{ $test->science?->title }}</td>
                                    <td class="cell-muted" data-label="Daraja">{{ $test->level }}</td>
                                    <td data-label="Davomiyligi">{{ $test->duration_minutes }} daqiqa</td>
                                    <td class="actions-cell">
                                        <form action="{{ route('admin.tests.sertifikat.destroy', $test) }}" method="POST"
                                            onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $sertifikatTests->links() }}
            @endif
        </div>
    </div>
@endsection
