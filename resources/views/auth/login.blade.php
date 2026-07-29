<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirish — Darslik</title>

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

        .form-check-input:checked {
            background-color: var(--teal);
            border-color: var(--teal);
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

        .status-box {
            background: var(--gold-soft);
            color: #7a5300;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .88rem;
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
                    <div class="auth-eyebrow mb-2">Xush kelibsiz</div>
                    <h3 class="mb-4">Hisobingizga kiring</h3>

                    @if (session('status'))
                        <div class="status-box mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required autofocus
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
                                autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-4">
                            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                            <label for="remember_me" class="form-check-label text-muted" style="font-size:.9rem;">
                                {{ __('Remember me') }}
                            </label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            @if (Route::has('password.request'))
                                <a class="link-muted" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @else
                                <span></span>
                            @endif

                            <button type="submit" class="btn btn-gold">
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </form>

                    @if (Route::has('register'))
                        <hr class="my-4" style="border-color:#E7E4DA;">
                        <p class="text-center text-muted small mb-0">
                            {{ __("Hisobingiz yo'qmi?") }}
                            <a href="{{ route('register') }}" class="fw-semibold"
                                style="color:var(--navy);text-decoration:none;">
                                {{ __("Ro'yxatdan o'ting") }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
