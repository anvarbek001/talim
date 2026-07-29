<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darslik — Video darslar orqali o'rganing</title>

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
            background: var(--paper);
        }

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Sora', sans-serif;
        }

        .navbar {
            background: var(--paper);
            border-bottom: 1px solid #E7E4DA;
        }

        .navbar-brand {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--navy) !important;
        }

        .navbar-brand span {
            color: var(--gold);
        }

        .btn-outline-navy {
            border: 1.5px solid var(--navy);
            color: var(--navy);
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-outline-navy:hover {
            background: var(--navy);
            color: #fff;
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy-deep);
            font-weight: 700;
            border-radius: 8px;
            border: none;
        }

        .btn-gold:hover {
            background: #e0961f;
            color: var(--navy-deep);
        }

        /* HERO */
        .hero {
            background: radial-gradient(1200px 500px at 80% -10%, #1c2550 0%, var(--navy) 55%, var(--navy-deep) 100%);
            color: #fff;
            padding: 90px 0 110px;
            position: relative;
            overflow: hidden;
        }

        .hero:before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(242, 169, 59, 0.10) 1.5px, transparent 1.5px);
            background-size: 26px 26px;
            opacity: 0.5;
        }

        .hero .eyebrow {
            color: var(--gold);
            letter-spacing: .08em;
            font-weight: 600;
            font-size: .85rem;
            text-transform: uppercase;
        }

        .hero h1 {
            font-weight: 800;
            font-size: 2.9rem;
            line-height: 1.15;
        }

        .hero p.lead {
            color: #C7CBE0;
            font-size: 1.1rem;
        }

        /* Video mock card — signature element */
        .lesson-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .45);
            overflow: hidden;
            transform: rotate(-1.2deg);
        }

        .lesson-thumb {
            background: linear-gradient(135deg, #233065 0%, #2F3B73 45%, #46579c 100%);
            height: 190px;
            position: relative;
        }

        .lesson-thumb .grid-lines {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .08) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .play-btn {
            width: 56px;
            height: 56px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy-deep);
            font-size: 1.4rem;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .3);
        }

        .lesson-progress {
            height: 4px;
            background: #E7E4DA;
        }

        .lesson-progress-fill {
            height: 100%;
            width: 62%;
            background: var(--gold);
        }

        .badge-live {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--teal);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
        }

        .badge-duration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(12, 16, 36, .75);
            color: #fff;
            font-size: .72rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
        }

        /* Stats strip */
        .stats-strip {
            background: var(--navy-deep);
            color: #fff;
            padding: 24px 0;
        }

        .stats-strip .num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--gold);
        }

        .stats-strip .lbl {
            font-size: .85rem;
            color: #AEB3C9;
        }

        /* Section headings */
        .section-eyebrow {
            color: var(--teal);
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-size: .8rem;
        }

        section {
            padding: 80px 0;
        }

        /* Steps */
        .step-card {
            background: #fff;
            border: 1px solid #E7E4DA;
            border-radius: 14px;
            padding: 32px 26px;
            height: 100%;
        }

        .step-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 2.2rem;
            color: var(--gold-soft);
            -webkit-text-stroke: 1.5px var(--navy);
            line-height: 1;
        }

        /* Subjects */
        .subject-pill {
            background: #fff;
            border: 1px solid #E7E4DA;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: .2s;
            height: 100%;
        }

        .subject-pill:hover {
            border-color: var(--gold);
            transform: translateY(-3px);
        }

        .subject-pill .icon {
            width: 46px;
            height: 46px;
            background: var(--navy);
            color: var(--gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.3rem;
        }

        /* Teacher cards */
        .teacher-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #E7E4DA;
            overflow: hidden;
        }

        .teacher-avatar {
            height: 140px;
            background: linear-gradient(135deg, var(--indigo), var(--navy));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2.2rem;
            font-weight: 800;
            font-family: 'Sora', sans-serif;
        }

        /* Pricing */
        .price-card {
            background: #fff;
            border: 1.5px solid #E7E4DA;
            border-radius: 16px;
            padding: 34px 28px;
            height: 100%;
        }

        .price-card.featured {
            border-color: var(--gold);
            box-shadow: 0 20px 40px -20px rgba(242, 169, 59, .35);
            position: relative;
        }

        .price-card.featured .ribbon {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gold);
            color: var(--navy-deep);
            font-size: .75rem;
            font-weight: 700;
            padding: 5px 16px;
            border-radius: 20px;
        }

        .price-amount {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 2.4rem;
            color: var(--navy);
        }

        .price-amount small {
            font-size: 1rem;
            font-weight: 500;
            color: var(--muted);
        }

        .check-list li {
            padding: 6px 0;
            font-size: .92rem;
        }

        .check-list i {
            color: var(--teal);
            margin-right: 8px;
        }

        /* Testimonials */
        .testimonial-card {
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            border: 1px solid #E7E4DA;
            height: 100%;
        }

        .avatar-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--gold-soft);
            color: var(--navy);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Final CTA */
        .final-cta {
            background: var(--navy);
            color: #fff;
            border-radius: 20px;
            padding: 56px 40px;
        }

        footer {
            background: var(--navy-deep);
            color: #AEB3C9;
            padding: 50px 0 20px;
            font-size: .9rem;
        }

        footer a {
            color: #D8DAE8;
            text-decoration: none;
        }

        footer a:hover {
            color: var(--gold);
        }

        footer h6 {
            color: #fff;
            font-family: 'Sora', sans-serif;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">Dars<span>lik</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto gap-lg-4">
                    <li class="nav-item"><a class="nav-link" href="#fanlar">Fanlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#oqituvchilar">O'qituvchilar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#narxlar">Obuna</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fikrlar">Fikrlar</a></li>
                </ul>
                <div class="d-flex gap-2 mt-3 mt-lg-0">
                    <a href="{{ route('login') }}" class="btn btn-outline-navy px-4">Kirish</a>
                    <a href="{{ route('register') }}" class="btn btn-gold px-4">Ro'yxatdan o'tish</a>
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
                        <a href="#narxlar" class="btn btn-gold btn-lg px-4">Obunani boshlash</a>
                        <a href="#fanlar" class="btn btn-outline-light btn-lg px-4">Fanlarni ko'rish</a>
                    </div>
                    <div class="d-flex gap-4 mt-4 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-camera-reels text-warning fs-5"></i>
                            <span class="small">1,200+ video dars</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people text-warning fs-5"></i>
                            <span class="small">150+ o'qituvchi</span>
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
                    <div class="num">1,200+</div>
                    <div class="lbl">Video darslar</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="num">150+</div>
                    <div class="lbl">O'qituvchilar</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="num">30,000+</div>
                    <div class="lbl">Faol o'quvchilar</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="num">18</div>
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
                        <p class="text-muted mb-0">Sizga kerakli fanlarni tanlang va mos obuna rejasini faollashtiring.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-num mb-3">03</div>
                        <h5 class="mb-2">Tomosha qiling, o'rganing</h5>
                        <p class="text-muted mb-0">Darslarni istalgan vaqtda tomosha qiling, mavzularni takrorlang.</p>
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
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="subject-pill">
                        <div class="icon"><i class="bi bi-calculator"></i></div>
                        <div class="fw-semibold">Matematika</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="subject-pill">
                        <div class="icon"><i class="bi bi-magnet"></i></div>
                        <div class="fw-semibold">Fizika</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="subject-pill">
                        <div class="icon"><i class="bi bi-translate"></i></div>
                        <div class="fw-semibold">Ingliz tili</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="subject-pill">
                        <div class="icon"><i class="bi bi-code-slash"></i></div>
                        <div class="fw-semibold">Dasturlash</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="subject-pill">
                        <div class="icon"><i class="bi bi-droplet-half"></i></div>
                        <div class="fw-semibold">Kimyo</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="subject-pill">
                        <div class="icon"><i class="bi bi-flower1"></i></div>
                        <div class="fw-semibold">Biologiya</div>
                    </div>
                </div>
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
                <div class="col-md-3 col-6">
                    <div class="teacher-card">
                        <div class="teacher-avatar">AK</div>
                        <div class="p-3">
                            <div class="fw-semibold">Aziz Karimov</div>
                            <div class="text-muted small">Matematika · 8 yil tajriba</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="teacher-card">
                        <div class="teacher-avatar">DY</div>
                        <div class="p-3">
                            <div class="fw-semibold">Dilnoza Yusupova</div>
                            <div class="text-muted small">Ingliz tili · 6 yil tajriba</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="teacher-card">
                        <div class="teacher-avatar">SR</div>
                        <div class="p-3">
                            <div class="fw-semibold">Sardor Rashidov</div>
                            <div class="text-muted small">Dasturlash · 5 yil tajriba</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="teacher-card">
                        <div class="teacher-avatar">NM</div>
                        <div class="p-3">
                            <div class="fw-semibold">Nilufar Mirzayeva</div>
                            <div class="text-muted small">Kimyo · 7 yil tajriba</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section id="narxlar" class="bg-white border-top border-bottom" style="border-color:#E7E4DA !important;">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">Obuna rejalari</div>
                <h2 class="mt-2">O'zingizga mos rejani tanlang</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="price-card">
                        <h5 class="mb-1">Boshlang'ich</h5>
                        <p class="text-muted small">Bitta fan bo'yicha darslar</p>
                        <div class="price-amount mb-3">49,000 <small>so'm/oy</small></div>
                        <ul class="list-unstyled check-list mb-4">
                            <li><i class="bi bi-check-circle-fill"></i>1 ta fan</li>
                            <li><i class="bi bi-check-circle-fill"></i>HD video darslar</li>
                            <li><i class="bi bi-check-circle-fill"></i>Mobil ilova</li>
                        </ul>
                        <a href="#" class="btn btn-outline-navy w-100">Tanlash</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="price-card featured">
                        <span class="ribbon">Mashhur</span>
                        <h5 class="mb-1">Standart</h5>
                        <p class="text-muted small">Barcha fanlarga to'liq kirish</p>
                        <div class="price-amount mb-3">99,000 <small>so'm/oy</small></div>
                        <ul class="list-unstyled check-list mb-4">
                            <li><i class="bi bi-check-circle-fill"></i>Barcha fanlar</li>
                            <li><i class="bi bi-check-circle-fill"></i>Testlar va topshiriqlar</li>
                            <li><i class="bi bi-check-circle-fill"></i>Sertifikat</li>
                        </ul>
                        <a href="#" class="btn btn-gold w-100">Tanlash</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="price-card">
                        <h5 class="mb-1">Premium</h5>
                        <p class="text-muted small">Guruh yoki oila uchun</p>
                        <div class="price-amount mb-3">179,000 <small>so'm/oy</small></div>
                        <ul class="list-unstyled check-list mb-4">
                            <li><i class="bi bi-check-circle-fill"></i>4 nafargacha profil</li>
                            <li><i class="bi bi-check-circle-fill"></i>Shaxsiy murabbiy yordami</li>
                            <li><i class="bi bi-check-circle-fill"></i>Offline yuklab olish</li>
                        </ul>
                        <a href="#" class="btn btn-outline-navy w-100">Tanlash</a>
                    </div>
                </div>
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
                        <p class="mb-0 text-muted">Bitta obuna bilan barcha bolalarim turli fanlarni o'rganishmoqda.
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
                <p class="mb-4" style="color:#C7CBE0;">Ro'yxatdan o'ting va birinchi haftada istalgan fandan bepul
                    darslarni tomosha qiling.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="#" class="btn btn-gold btn-lg px-4">Ro'yxatdan o'tish</a>
                    <a href="#" class="btn btn-outline-light btn-lg px-4">Kirish</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <h5 class="text-white mb-3">Dars<span style="color:var(--gold);">lik</span></h5>
                    <p class="small">O'qituvchilar video darslar joylaydigan, o'quvchilar esa obuna orqali
                        o'rganadigan ta'lim platformasi.</p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="mb-3">Platforma</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="#fanlar">Fanlar</a></li>
                        <li><a href="#oqituvchilar">O'qituvchilar</a></li>
                        <li><a href="#narxlar">Obuna</a></li>
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
                    <a href="#" class="btn btn-outline-light btn-sm">Ariza topshirish</a>
                </div>
            </div>
            <hr class="my-4" style="border-color:#2A3157;">
            <div class="d-flex justify-content-between flex-wrap gap-2 small">
                <span>© 2026 Darslik. Barcha huquqlar himoyalangan.</span>
                <span>Toshkent, O'zbekiston</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
