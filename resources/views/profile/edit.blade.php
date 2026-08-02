@php
    $isTeacher = auth()->user()->hasRole('teacher');
@endphp

@extends($isTeacher ? 'layouts.teacher' : 'layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Sozlamalar</h1>
                <p class="page-sub">Hisobingiz bilan bog'liq sozlamalar shu yerda.</p>
            </div>
        </div>

        @if ($isTeacher)
            <div class="card fade-up settings-card" style="animation-delay:.04s;">
                @include('profile.partials.update-profile-information-form')
            </div>
        @endif

        <div class="card fade-up settings-card" style="animation-delay:.1s;">
            @include('profile.partials.update-password-form')
        </div>

        @unless ($isTeacher)
            <div class="card fade-up settings-card" style="animation-delay:.16s;">
                @include('profile.partials.theme-toggle')
            </div>
        @endunless

        @if ($isTeacher)
            <div class="card fade-up settings-card is-danger" style="animation-delay:.22s;">
                @include('profile.partials.delete-user-form')
            </div>
        @endif
    </div>

    <style>
        .page-head h1 {
            font-size: 1.4rem;
            margin: 4px 0 4px;
        }

        .page-sub {
            color: var(--muted);
            font-size: .86rem;
            margin: 0;
        }

        .settings-card {
            margin-bottom: 16px;
        }

        .settings-card:last-child {
            margin-bottom: 0;
        }

        .settings-card header h2 {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text);
            margin: 0 0 4px;
        }

        .settings-card header p {
            color: var(--muted);
            font-size: .82rem;
            margin: 0 0 18px;
        }

        .settings-card.is-danger header h2 {
            color: var(--coral);
        }
    </style>
@endsection
