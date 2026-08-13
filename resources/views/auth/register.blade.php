<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title>Ro'yxatdan o'tish — DarsQil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --navy: #141B33;
            --navy-deep: #0C1024;
            --indigo: #2F3B73;
            --gold: #F2A93B;
            --gold-soft: #FCEBC7;
            --teal: #1F8A70;
            --paper: #F7F6F2;
            --ink: #1B1B18;
            --muted: #6B7280;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background: radial-gradient(1200px 500px at 80% -10%, #1c2550 0%, var(--navy) 55%, var(--navy-deep) 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        body:before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(242, 169, 59, 0.10) 1.5px, transparent 1.5px);
            background-size: 26px 26px;
            opacity: 0.5;
            pointer-events: none;
        }

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Sora', sans-serif;
        }

        .brand-link {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: #fff;
            text-decoration: none;
        }

        .brand-link span {
            color: var(--gold);
        }

        .auth-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .45);
            padding: 44px 40px;
        }

        .auth-eyebrow {
            color: var(--teal);
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-size: .78rem;
        }

        .form-label {
            font-weight: 600;
            font-size: .9rem;
            color: var(--ink);
        }

        .form-control {
            border-radius: 9px;
            border: 1.5px solid #E7E4DA;
            padding: 10px 14px;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(242, 169, 59, .2);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy-deep);
            font-weight: 700;
            border-radius: 9px;
            border: none;
            padding: 10px 26px;
        }

        .btn-gold:hover {
            background: #e0961f;
            color: var(--navy-deep);
        }

        .link-muted {
            color: var(--muted);
            font-size: .88rem;
            text-decoration: underline;
        }

        .link-muted:hover {
            color: var(--navy);
        }

        /* ROLE SELECT — segmented control */
        .role-select {
            display: flex;
            gap: 10px;
        }

        .role-option {
            flex: 1;
            position: relative;
        }

        .role-option input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
            margin: 0;
        }

        .role-option label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1.5px solid #E7E4DA;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: .15s;
        }

        .role-option label i {
            font-size: 1.1rem;
        }

        .role-option input:checked+label {
            border-color: var(--gold);
            background: var(--gold-soft);
            color: var(--ink);
        }

        .role-option input:focus-visible+label {
            box-shadow: 0 0 0 3px rgba(242, 169, 59, .25);
        }
    </style>
</head>

<body>

    <div class="container position-relative py-5">
        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="brand-link">Dars<span>lik</span></a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <div class="auth-eyebrow mb-2">Bizga qo'shiling</div>
                    <h3 class="mb-4">Yangi hisob yarating</h3>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required autofocus
                                autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label class="form-label d-block">{{ __("Ro'l") }}</label>
                            <div class="role-select">
                                <div class="role-option">
                                    <input type="radio" id="role_student" name="role" value="student"
                                        {{ old('role', 'student') == 'student' ? 'checked' : '' }} required>
                                    <label for="role_student">
                                        <i class="bi bi-mortarboard"></i> O'quvchiman
                                    </label>
                                </div>
                                <div class="role-option">
                                    <input type="radio" id="role_teacher" name="role" value="teacher"
                                        {{ old('role') == 'teacher' ? 'checked' : '' }} required>
                                    <label for="role_teacher">
                                        <i class="bi bi-person-workspace"></i> O'qituvchiman
                                    </label>
                                </div>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required
                                autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror" required
                                autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <a class="link-muted" href="{{ route('login') }}">
                                {{ __('Already registered?') }}
                            </a>

                            <button type="submit" class="btn btn-gold">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>

                    @if (config('services.google.client_id'))
                        <div class="d-flex align-items-center gap-3 my-4">
                            <hr class="flex-grow-1" style="border-color:#E7E4DA;">
                            <span class="text-muted small">yoki</span>
                            <hr class="flex-grow-1" style="border-color:#E7E4DA;">
                        </div>
                        {{-- Tanlangan rol (o'quvchi/o'qituvchi) bosilgan paytda ?role= sifatida qo'shib yuboriladi. --}}
                        <button type="button" id="googleSignupBtn" class="btn w-100 d-flex align-items-center justify-content-center gap-2"
                            style="border-radius:9px;border:1.5px solid #E7E4DA;color:var(--ink);font-weight:600;padding:10px 26px;">
                            <i class="bi bi-google" style="color:var(--gold);"></i> {{ __("Google orqali ro'yxatdan o'tish") }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('googleSignupBtn')?.addEventListener('click', function () {
            const role = document.querySelector('input[name="role"]:checked')?.value || 'student';
            window.location.href = "{{ route('google.redirect') }}?role=" + encodeURIComponent(role);
        });
    </script>
</body>

</html>
