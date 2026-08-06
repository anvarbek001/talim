@extends('layouts.teacher')
@section('content')
    <div class="page">
        <!-- STAT CARDS -->
        <div class="stats-grid">
            @if (config('features.lessons_enabled'))
                <div class="stat-card fade-up" style="animation-delay:.02s;">
                    <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                            class="bi bi-camera-reels"></i></div>
                    <div class="stat-num" data-count="86">0</div>
                    <div class="stat-lbl">Jami darslarim</div>
                    <div class="stat-delta up"><i class="bi bi-arrow-up-short"></i> +4 shu oy</div>
                </div>
            @endif
            <div class="stat-card fade-up" style="animation-delay:.08s;">
                <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i class="bi bi-people"></i>
                </div>
                <div class="stat-num" data-count="2140">0</div>
                <div class="stat-lbl">Obunachilarim</div>
                <div class="stat-delta up"><i class="bi bi-arrow-up-short"></i> +126 shu oy</div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.14s;">
                <div class="stat-icon" style="background:var(--amber-soft);color:#8A6100;"><i class="bi bi-eye"></i>
                </div>
                <div class="stat-num" data-count="48200">0</div>
                <div class="stat-lbl">Umumiy ko'rishlar</div>
                <div class="stat-delta up"><i class="bi bi-arrow-up-short"></i> +8.4%</div>
            </div>
            <div class="stat-card fade-up" style="animation-delay:.2s;">
                <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i
                        class="bi bi-wallet2"></i></div>
                <div class="stat-num" data-count="4820000">0</div>
                <div class="stat-lbl">Oylik daromad (so'm)</div>
                <div class="stat-delta up"><i class="bi bi-arrow-up-short"></i> +12%</div>
            </div>
        </div>

        <div class="grid-main">
            <!-- LEFT -->
            <div>
                @if (config('features.lessons_enabled'))
                    <div class="card fade-up" style="animation-delay:.1s;">
                        <div class="card-head">
                            <div class="card-title">So'nggi darslar</div>
                            <a href="#" class="card-link">Barchasini ko'rish</a>
                        </div>

                        <div class="lesson-row">
                            <div class="lesson-thumb" style="background:linear-gradient(135deg,var(--primary),#9C8CFF);"><i
                                    class="bi bi-play-fill"></i></div>
                            <div class="lesson-info">
                                <div class="lesson-title">Kvadrat tenglamalar — 3-qism</div>
                                <div class="lesson-sub">Matematika · 24:18</div>
                            </div>
                            <div class="lesson-views"><i class="bi bi-eye"></i> 1,204</div>
                            <span class="badge-pill on">Nashr etilgan</span>
                        </div>

                        <div class="lesson-row">
                            <div class="lesson-thumb" style="background:linear-gradient(135deg,var(--mint),#33D6A0);">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div class="lesson-info">
                                <div class="lesson-title">Trigonometriya asoslari</div>
                                <div class="lesson-sub">Matematika · 31:05</div>
                            </div>
                            <div class="lesson-views"><i class="bi bi-eye"></i> 892</div>
                            <span class="badge-pill on">Nashr etilgan</span>
                        </div>

                        <div class="lesson-row">
                            <div class="lesson-thumb" style="background:linear-gradient(135deg,var(--amber),#FFCB6B);"><i
                                    class="bi bi-play-fill"></i></div>
                            <div class="lesson-info">
                                <div class="lesson-title">Integral — kirish darsi</div>
                                <div class="lesson-sub">Matematika · 19:42</div>
                            </div>
                            <div class="lesson-views"><i class="bi bi-eye"></i> —</div>
                            <span class="badge-pill wait">Ko'rib chiqilmoqda</span>
                        </div>
                    </div>
                @endif

                <div class="card fade-up" style="animation-delay:.16s;">
                    <div class="card-head">
                        <div class="card-title">Haftalik faollik</div>
                        <span class="card-link" style="color:var(--muted);">Ko'rishlar soni</span>
                    </div>
                    <div class="week-chart">
                        <div class="week-col">
                            <div class="week-bar" style="height:45%;animation-delay:.05s;"></div>
                            <div class="week-lbl">Dush</div>
                        </div>
                        <div class="week-col">
                            <div class="week-bar" style="height:62%;animation-delay:.1s;"></div>
                            <div class="week-lbl">Sesh</div>
                        </div>
                        <div class="week-col">
                            <div class="week-bar" style="height:38%;animation-delay:.15s;"></div>
                            <div class="week-lbl">Chor</div>
                        </div>
                        <div class="week-col">
                            <div class="week-bar" style="height:80%;animation-delay:.2s;"></div>
                            <div class="week-lbl">Pay</div>
                        </div>
                        <div class="week-col">
                            <div class="week-bar" style="height:70%;animation-delay:.25s;"></div>
                            <div class="week-lbl">Jum</div>
                        </div>
                        <div class="week-col">
                            <div class="week-bar" style="height:92%;animation-delay:.3s;"></div>
                            <div class="week-lbl">Shan</div>
                        </div>
                        <div class="week-col">
                            <div class="week-bar" style="height:55%;animation-delay:.35s;"></div>
                            <div class="week-lbl">Yak</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div>
                <div class="card fade-up" style="animation-delay:.14s;">
                    <div class="card-head">
                        <div class="card-title">Yangi sharhlar</div>
                    </div>

                    <div class="comment-row">
                        <div class="comment-avatar" style="background:var(--primary);">M</div>
                        <div>
                            <div class="comment-name">Madina J.</div>
                            <div class="comment-text">Kvadrat tenglamalar darsi juda tushunarli tushuntirilgan,
                                rahmat!</div>
                            <div class="comment-meta">2 soat oldin</div>
                        </div>
                    </div>
                    <div class="comment-row">
                        <div class="comment-avatar" style="background:var(--coral);">J</div>
                        <div>
                            <div class="comment-name">Jasur T.</div>
                            <div class="comment-text">Trigonometriya bo'yicha yana bir dars qo'shsangiz zo'r
                                bo'lardi.</div>
                            <div class="comment-meta">5 soat oldin</div>
                        </div>
                    </div>
                    <div class="comment-row">
                        <div class="comment-avatar" style="background:var(--mint);">Z</div>
                        <div>
                            <div class="comment-name">Zilola S.</div>
                            <div class="comment-text">Misollar yaxshi tanlangan, imtihonga tayyorlanishga yordam
                                berdi.</div>
                            <div class="comment-meta">1 kun oldin</div>
                        </div>
                    </div>
                </div>

                <div class="card fade-up" style="animation-delay:.22s;">
                    <div class="card-head">
                        <div class="card-title">Tezkor amallar</div>
                    </div>
                    @if (config('features.lessons_enabled'))
                        <a href="#" class="qa-btn">
                            <div class="qa-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                                    class="bi bi-cloud-upload"></i></div> Video yuklash
                        </a>
                    @endif
                    <a href="#" class="qa-btn">
                        <div class="qa-icon" style="background:var(--mint-soft);color:var(--mint);"><i
                                class="bi bi-patch-question"></i></div> Test yaratish
                    </a>
                    <a href="#" class="qa-btn">
                        <div class="qa-icon" style="background:var(--amber-soft);color:#8A6100;"><i
                                class="bi bi-megaphone"></i></div> Xabarnoma yuborish
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
