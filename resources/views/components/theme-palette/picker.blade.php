@props(['surface' => 'account'])
<div class="bkb-palette" data-bkb-palette data-surface="{{ $surface }}">
    <button type="button" class="bkb-palette__toggle" data-palette-toggle aria-expanded="false" aria-label="Open theme palette" title="Theme palette">
        <span aria-hidden="true">◉</span>
    </button>
    <section class="bkb-palette__panel" data-palette-panel hidden aria-label="Theme palette">
        <header><strong>Operational accent</strong><small>Saved to your account</small></header>
        <div class="bkb-palette__swatches">
            @foreach (['#9CFF3F'=>'BiKuBe Lime','#22D3EE'=>'Dispatch Cyan','#A855F7'=>'Support Purple','#F59E0B'=>'Finance Amber','#EF4444'=>'Security Red','#22C55E'=>'Worker Green','#3B82F6'=>'Ocean Blue','#FF7591'=>'Rose','#FE9544'=>'Orange','#8BD3FF'=>'Nordic Ice'] as $hex=>$name)
                <button type="button" data-palette-swatch="{{ $hex }}" style="--swatch:{{ $hex }}" aria-label="{{ $name }}" title="{{ $name }}"></button>
            @endforeach
        </div>
        <label>Custom HEX <input data-palette-hex value="#9CFF3F" maxlength="7" pattern="#[0-9A-Fa-f]{6}"></label>
        <input data-palette-color type="color" value="#9CFF3F" aria-label="Choose custom color">
        <div class="bkb-palette__actions"><button type="button" data-palette-save>Save</button><button type="button" data-palette-reset>Reset</button></div>
        <p data-palette-status role="status"></p>
    </section>
</div>
