@php
    $prefix = $prefix ?? 'default';
    $withBlocks = $withBlocks ?? false;
@endphp

{{-- ========================================================= --}}
{{-- SAVOLLAR REJIMI — "Qo'lda kiritish" / "Excel yuklash"       --}}
{{-- ========================================================= --}}
<div class="qmode-toggle" data-qmode-toggle>
    <button type="button" class="mode-btn active" data-mode="manual">Qo'lda kiritish</button>
    <button type="button" class="mode-btn" data-mode="excel">Excel yuklash</button>
</div>

<div class="qmode-panel" data-qmode-panel="manual">
    @if ($withBlocks)
        <div class="qb-block-hint">
            <div><strong>1-blok</strong> — asosiy fan, 30 ta savol, savol boshiga 3.1 ball</div>
            <div><strong>2-blok</strong> — qo'shimcha fan, 30 ta savol, savol boshiga 2.1 ball</div>
            <div><strong>3-blok</strong> — majburiy fanlar (Ona tili, Matematika, Tarix), har biridan 10 tadan savol, savol boshiga 1.1 ball</div>
        </div>
        <div class="qb-block-counts" data-qb-block-counts>
            <span data-count="block1">1-blok: 0/30</span>
            <span data-count="block2">2-blok: 0/30</span>
            <span data-count="ona_tili">Ona tili: 0/10</span>
            <span data-count="matematika">Matematika: 0/10</span>
            <span data-count="tarix">Tarix: 0/10</span>
        </div>
    @endif
    <div class="qb-list"></div>

    <button type="button" class="btn-ghost qb-add-question mb-16">
        <i class="bi bi-plus-lg"></i> Savol qo'shish
    </button>
</div>

<div class="qmode-panel" data-qmode-panel="excel" style="display:none;">
    @if ($withBlocks)
        @php
            $excelSlots = [
                ['key' => 'block1', 'label' => "1-blok — asosiy fan (30 ta savol)"],
                ['key' => 'block2', 'label' => "2-blok — qo'shimcha fan (30 ta savol)"],
                ['key' => 'block3_ona_tili', 'label' => 'Ona tili (10 ta savol)'],
                ['key' => 'block3_matematika', 'label' => 'Matematika (10 ta savol)'],
                ['key' => 'block3_tarix', 'label' => 'Tarix (10 ta savol)'],
            ];
        @endphp
        <div class="field mb-16">
            <a href="{{ route('tests.questions-template') }}" class="template-link">
                <i class="bi bi-download"></i> Namuna shablonni yuklab olish
            </a>
            <div class="field-hint mb-12">
                Shablondagi ustunlar: <strong>savol</strong>, <strong>variant_1..variant_4</strong>,
                <strong>togri_variant</strong> (1-4 raqam). Har bir blok/fan uchun alohida fayl yuklang —
                fayldagi savollar soni ko'rsatilgan miqdorga aniq mos kelishi kerak.
            </div>
            @foreach ($excelSlots as $slot)
                <div class="mb-12">
                    <label class="field-label">{{ $slot['label'] }}</label>
                    <label class="dropzone" id="{{ $prefix }}_{{ $slot['key'] }}_dropzone" for="{{ $prefix }}_{{ $slot['key'] }}_questions_file">
                        <div class="dropzone-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                        <div class="dropzone-text">Excel faylni shu yerga tashlang yoki <span>tanlash uchun bosing</span></div>
                        <div class="dropzone-hint">XLSX yoki XLS — maksimal 5 MB</div>
                        <input type="file" name="{{ $slot['key'] }}_questions_file" id="{{ $prefix }}_{{ $slot['key'] }}_questions_file" accept=".xlsx,.xls" hidden>
                    </label>
                    <div class="file-list" id="{{ $prefix }}_{{ $slot['key'] }}_file_list"></div>
                </div>
            @endforeach
        </div>
    @else
        <div class="field mb-16">
            <label class="field-label">Savollar fayli</label>
            <a href="{{ route('tests.questions-template') }}" class="template-link">
                <i class="bi bi-download"></i> Namuna shablonni yuklab olish
            </a>
            <label class="dropzone" id="{{ $prefix }}_dropzone" for="{{ $prefix }}_questions_file">
                <div class="dropzone-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                <div class="dropzone-text">Excel faylni shu yerga tashlang yoki <span>tanlash uchun bosing</span></div>
                <div class="dropzone-hint">XLSX yoki XLS — maksimal 5 MB</div>
                <input type="file" name="questions_file" id="{{ $prefix }}_questions_file" accept=".xlsx,.xls" hidden>
            </label>
            <div class="file-list" id="{{ $prefix }}_file_list"></div>
            <div class="field-hint">
                Shablondagi ustunlar: <strong>savol</strong>, <strong>variant_1..variant_4</strong>,
                <strong>togri_variant</strong> (1-4 raqam). Har bir qator — bitta savol.
            </div>
        </div>
    @endif
</div>

<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            @if ($withBlocks)
                ['block1', 'block2', 'block3_ona_tili', 'block3_matematika', 'block3_tarix'].forEach(function(key) {
                    window.initQuestionsDropzone && window.initQuestionsDropzone('{{ $prefix }}_' + key);
                });
            @else
                window.initQuestionsDropzone && window.initQuestionsDropzone('{{ $prefix }}');
            @endif
        });
    })();
</script>
