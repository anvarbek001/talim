<!DOCTYPE html>
<html lang="uz">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @php
            $seoTitle =
                "DarsQil — Video darslar orqali onlayn ta'lim platformasi | Matematika, Fizika, DTM tayyorgarlik";
            $seoDescription =
                "DarsQil'da tajribali o'qituvchilardan video darslarni tomosha qiling: matematika, " .
                "fizika, dasturlash va boshqa fanlar. Obuna orqali onlayn ta'lim oling, DTM va sertifikat " .
                "testlariga tayyorgarlik ko'ring. " .
                number_format($overview['lessons_count'], 0, '.', ' ') .
                ' ta video dars, ' .
                number_format($overview['teachers_count'], 0, '.', ' ') .
                " ta o'qituvchi.";
            $seoUrl = rtrim(config('seo.url'), '/') . '/';
        @endphp
        @include('partials.seo-meta')

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        <link
            href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        {{-- JSON-LD: Google va Yandex uchun structured data (rich snippet imkoniyati) --}}
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => 'DarsQil',
            'alternateName' => "DarsQil — onlayn ta'lim platformasi",
            'url' => rtrim(config('seo.url'), '/') . '/',
            'logo' => asset('favicon.ico'),
            'description' => $seoDescription,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Toshkent',
                'addressCountry' => 'UZ',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'DarsQil',
            'url' => rtrim(config('seo.url'), '/') . '/',
            'inLanguage' => 'uz',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

        @vite(['resources/css/welcome.css'])
    </head>

    <body>

        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg py-3 sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('welcome') }}">Dars<span>Qil</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav mx-auto gap-lg-4">
                        <li class="nav-item"><a class="nav-link" href="#fanlar">Fanlar</a></li>
                        <li class="nav-item"><a class="nav-link" href="#oqituvchilar">O'qituvchilar</a></li>
                        <li class="nav-item"><a class="nav-link" href="#fikrlar">Fikrlar</a></li>
                    </ul>
                    <div class="d-flex gap-2 mt-3 mt-lg-0">
                        <a href="{{ route('login') }}" class="btn btn-outline-navy px-4">Kirish</a>
                        <a href="{{ route('register') }}" class="btn btn-gold px-4">Ro'yxatdan o'tish</a>
                        <a href="{{ route('loginUniversity') }}" class="btn btn-outline-navy px-4">OTM uchun</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- HERO -->
        <header class="hero">
            <div class="container position-relative">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6">
                        <div class="eyebrow mb-3">Video darslar platformasi</div>
                        <h1 class="mb-3">O'qituvchilardan darslarni tomosha qiling, o'z sur'atingizda o'rganing</h1>
                        <p class="lead mb-4">Matematika, fizika, dasturlash va boshqa fanlar bo'yicha tajribali
                            o'qituvchilarning video darslariga bir oylik obuna orqali kirish oling.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="btn btn-gold btn-lg px-4">Ro'yxatdan o'tish</a>
                            <a href="#fanlar" class="btn btn-outline-light btn-lg px-4">Fanlarni ko'rish</a>
                        </div>
                        <div class="d-flex gap-4 mt-4 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-camera-reels text-warning fs-5"></i>
                                <span class="small">{{ number_format($overview['lessons_count'], 0, '.', ' ') }} video
                                    dars</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-people text-warning fs-5"></i>
                                <span class="small">{{ number_format($overview['teachers_count'], 0, '.', ' ') }}
                                    o'qituvchi</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="lesson-card mx-auto" style="max-width:380px;">
                            <div class="lesson-thumb">
                                <div class="grid-lines"></div>
                                <span class="badge-live">JONLI</span>
                                <div class="play-btn"><i class="bi bi-play-fill"></i></div>
                                <span class="badge-duration">24:18</span>
                            </div>
                            <div class="lesson-progress">
                                <div class="lesson-progress-fill"></div>
                            </div>
                            <div class="p-3">
                                <div class="fw-semibold">Matematika · Kvadrat tenglamalar</div>
                                <div class="text-muted small mt-1">O'qituvchi: Aziz Karimov</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- STATS STRIP -->
        <div class="stats-strip">
            <div class="container">
                <div class="row text-center gy-3">
                    <div class="col-6 col-md-3">
                        <div class="num">{{ number_format($overview['lessons_count'], 0, '.', ' ') }}</div>
                        <div class="lbl">Video darslar</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="num">{{ number_format($overview['teachers_count'], 0, '.', ' ') }}</div>
                        <div class="lbl">O'qituvchilar</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="num">{{ number_format($overview['students_count'], 0, '.', ' ') }}</div>
                        <div class="lbl">Faol o'quvchilar</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="num">{{ number_format($sciencesCount, 0, '.', ' ') }}</div>
                        <div class="lbl">Fan yo'nalishi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HOW IT WORKS -->
        <section>
            <div class="container">
                <div class="text-center mb-5">
                    <div class="section-eyebrow">Qanday ishlaydi</div>
                    <h2 class="mt-2">Uch qadamda o'rganishni boshlang</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="step-card">
                            <div class="step-num mb-3">01</div>
                            <h5 class="mb-2">Ro'yxatdan o'ting</h5>
                            <p class="text-muted mb-0">Bir necha daqiqada hisob yarating va profilingizni sozlang.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step-card">
                            <div class="step-num mb-3">02</div>
                            <h5 class="mb-2">Fan va obuna tanlang</h5>
                            <p class="text-muted mb-0">Sizga kerakli fanlarni tanlang va mos obuna rejasini
                                faollashtiring.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step-card">
                            <div class="step-num mb-3">03</div>
                            <h5 class="mb-2">Tomosha qiling, o'rganing</h5>
                            <p class="text-muted mb-0">Darslarni istalgan vaqtda tomosha qiling, mavzularni takrorlang.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SUBJECTS -->
        <section id="fanlar" class="bg-white border-top border-bottom" style="border-color:#E7E4DA !important;">
            <div class="container">
                <div class="text-center mb-5">
                    <div class="section-eyebrow">Fanlar</div>
                    <h2 class="mt-2">Har bir fan bo'yicha video darslar</h2>
                </div>
                <div class="row g-3 g-md-4">
                    @forelse ($sciences as $science)
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="{{ route('register') }}"
                                class="subject-pill d-block text-decoration-none text-reset">
                                <div class="icon" style="background:{{ $science->color }};color:#fff;">
                                    <i class="bi {{ $science->icon }}"></i>
                                </div>
                                <div class="fw-semibold">{{ $science->title }}</div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">Hozircha fanlar qo'shilmagan.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- TEACHERS -->
        <section id="oqituvchilar">
            <div class="container">
                <div class="text-center mb-5">
                    <div class="section-eyebrow">O'qituvchilar</div>
                    <h2 class="mt-2">Tajribali mutaxassislardan o'rganing</h2>
                </div>
                <div class="row g-4">
                    @forelse ($teachers as $teacher)
                        <div class="col-md-3 col-6">
                            <div class="teacher-card">
                                <div class="teacher-avatar">{{ $teacher->initials() }}</div>
                                <div class="p-3">
                                    <div class="fw-semibold">{{ $teacher->name }}</div>
                                    <div class="text-muted small">
                                        {{ $teacher->lessons->first()?->science?->title ?? "O'qituvchi" }}
                                        · {{ $teacher->lessons_count }} ta dars
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">Hozircha o'qituvchilar qo'shilmagan.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section id="fikrlar">
            <div class="container">
                <div class="text-center mb-5">
                    <div class="section-eyebrow">Fikrlar</div>
                    <h2 class="mt-2">O'quvchilarimiz nima deydi</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle">M</div>
                                <div>
                                    <div class="fw-semibold">Madina, 11-sinf</div>
                                    <div class="text-muted small">Matematika obunachisi</div>
                                </div>
                            </div>
                            <p class="mb-0 text-muted">Darslarni istalgan vaqtda qayta ko'rish imkoniyati juda qulay,
                                imtihonlarga tayyorgarlik osonlashdi.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle">J</div>
                                <div>
                                    <div class="fw-semibold">Jasur, talaba</div>
                                    <div class="text-muted small">Dasturlash obunachisi</div>
                                </div>
                            </div>
                            <p class="mb-0 text-muted">O'qituvchilar amaliy misollar bilan tushuntiradi, bu meni ish
                                topishimga yordam berdi.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle">Z</div>
                                <div>
                                    <div class="fw-semibold">Zilola, ona</div>
                                    <div class="text-muted small">Premium obunachi</div>
                                </div>
                            </div>
                            <p class="mb-0 text-muted">Bitta obuna bilan barcha bolalarim turli fanlarni
                                o'rganishmoqda.
                                Juda tejamli.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FINAL CTA -->
        <section class="pt-0">
            <div class="container">
                <div class="final-cta text-center">
                    <h2 class="mb-3">Bugundan boshlab o'rganishni boshlang</h2>
                    <p class="mb-4" style="color:#C7CBE0;">Ro'yxatdan o'ting va birinchi haftada istalgan fandan
                        bepul
                        darslarni tomosha qiling.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-gold btn-lg px-4">Ro'yxatdan o'tish</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">Kirish</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer>
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-4">
                        <h5 class="text-white mb-3">Dars<span style="color:var(--gold);">Qil</span></h5>
                        <p class="small">O'qituvchilar video darslar joylaydigan, o'quvchilar esa obuna orqali
                            o'rganadigan ta'lim platformasi.</p>
                    </div>
                    <div class="col-lg-2 col-6">
                        <h6 class="mb-3">Platforma</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="#fanlar">Fanlar</a></li>
                            <li><a href="#oqituvchilar">O'qituvchilar</a></li>
                            <li><a href="#fikrlar">Fikrlar</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-6">
                        <h6 class="mb-3">Kompaniya</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="#">Biz haqimizda</a></li>
                            <li><a href="#">Aloqa</a></li>
                            <li><a href="#">Vakansiyalar</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h6 class="mb-3">O'qituvchi bo'lishni xohlaysizmi?</h6>
                        <p class="small mb-3">Darslaringizni joylang va o'quvchilarga bilim ulashing.</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">Ariza topshirish</a>
                    </div>
                    <div class="col-lg-4 d-flex gap-3">
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <a href="https://t.me/Anvarbek_Ergashev">Muallif:Anvarbek Ergashev</a>
                        </ul>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <a type="tel:+998938731809">Aloqa uchun:+998938731809</a>
                        </ul>
                    </div>
                </div>
                <hr class="my-4" style="border-color:#2A3157;">
                <div class="d-flex justify-content-between flex-wrap gap-2 small">
                    <span>© 2026 DarsQil. Barcha huquqlar himoyalangan.</span>
                    <span>Toshkent, O'zbekiston</span>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>
