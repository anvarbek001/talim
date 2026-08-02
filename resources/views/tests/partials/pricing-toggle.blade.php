@php
    $price = $price ?? 0;
@endphp
<div class="field">
    <label class="field-label">Narx turi</label>
    <div class="qmode-toggle" data-pricing-toggle="{{ $prefix }}">
        <button type="button" class="mode-btn" data-pricing-mode="free">Bepul</button>
        <button type="button" class="mode-btn" data-pricing-mode="paid">Pullik</button>
    </div>
</div>
<div class="field" data-pricing-panel="{{ $prefix }}">
    <label class="field-label">Narx (so'm)</label>
    <input type="number" name="price" id="{{ $prefix }}_price" class="text-control" min="1"
        value="{{ $price > 0 ? $price : '' }}">
</div>
