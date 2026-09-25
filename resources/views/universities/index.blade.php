@extends('layouts.university')
@section('body')
    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Statistika</h1>
                <p class="page-sub">Loyihaning umumiy ko'rsatkichlari va so'nggi faoliyat.</p>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6 col-lg-3">
                <div class="card mb-0 stat-card">
                    <div class="stat-top">
                        <span class="cell-muted">Foydalanuvchilar</span>
                        <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                                class="bi bi-people"></i></div>
                    </div>
                    <div class="stat-value">1,248</div>
                    <div class="stat-delta">+86 shu oy</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card mb-0 stat-card">
                    <div class="stat-top">
                        <span class="cell-muted">Kitoblar</span>
                        <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i
                                class="bi bi-file-earmark-pdf"></i></div>
                    </div>
                    <div class="stat-value">64</div>
                    <div class="stat-delta">+4 shu oy</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card mb-0 stat-card">
                    <div class="stat-top">
                        <span class="cell-muted">Xaridlar</span>
                        <div class="stat-icon" style="background:var(--amber-soft);color:var(--amber);"><i
                                class="bi bi-receipt"></i></div>
                    </div>
                    <div class="stat-value">532</div>
                    <div class="stat-delta">+41 shu oy</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card mb-0 stat-card">
                    <div class="stat-top">
                        <span class="cell-muted">Tushum (jami)</span>
                        <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i
                                class="bi bi-credit-card-2-front"></i></div>
                    </div>
                    <div class="stat-value">48.6M so'm</div>
                    <div class="stat-delta">+6.2M shu oy</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-head">
                        <div>
                            <div class="card-title">Ro'yxatdan o'tishlar</div>
                            <div class="card-sub-text">So'nggi 7 kun</div>
                        </div>
                    </div>
                    <canvas id="usersChart" height="110"></canvas>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">So'nggi xaridlar</div>
                    </div>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Foydalanuvchi</th>
                                    <th>Mahsulot</th>
                                    <th>Sana</th>
                                    <th>Holat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="cell-strong">Aziz Karimov</td>
                                    <td class="cell-muted">Matematika kursi</td>
                                    <td class="cell-muted">23.09.2026 14:20</td>
                                    <td><span class="badge-role">to'langan</span></td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Dilnoza Yusupova</td>
                                    <td class="cell-muted">Ingliz tili A1</td>
                                    <td class="cell-muted">23.09.2026 11:05</td>
                                    <td><span class="badge-role pending">kutilmoqda</span></td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Jasur Toshpulatov</td>
                                    <td class="cell-muted">Fizika testlari</td>
                                    <td class="cell-muted">22.09.2026 19:42</td>
                                    <td><span class="badge-role">to'langan</span></td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Malika Rahimova</td>
                                    <td class="cell-muted">Kimyo kitobi</td>
                                    <td class="cell-muted">22.09.2026 09:17</td>
                                    <td><span class="badge-role">to'langan</span></td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Sardor Nabiyev</td>
                                    <td class="cell-muted">Video dars to'plami</td>
                                    <td class="cell-muted">21.09.2026 16:50</td>
                                    <td><span class="badge-role pending">kutilmoqda</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Yangi foydalanuvchilar</div>
                    </div>
                    <div class="table-wrap">
                        <table class="data-table" style="min-width:0;">
                            <tbody>
                                <tr>
                                    <td class="cell-strong">Kamola Sattorova</td>
                                    <td class="cell-muted">5 daqiqa oldin</td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Bekzod Yoldashev</td>
                                    <td class="cell-muted">32 daqiqa oldin</td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Nilufar Ergasheva</td>
                                    <td class="cell-muted">1 soat oldin</td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Otabek Mirzayev</td>
                                    <td class="cell-muted">3 soat oldin</td>
                                </tr>
                                <tr>
                                    <td class="cell-strong">Zarina Qodirova</td>
                                    <td class="cell-muted">Kecha</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Bo'limlar bo'yicha xarid</div>
                    </div>
                    <canvas id="categoryChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
