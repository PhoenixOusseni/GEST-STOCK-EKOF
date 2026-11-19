@php
    $nomMagazin = trim((string) ($magasinImpression ?? $magasin ?? $magasinLabel ?? ''));
    if ($nomMagazin === '' || $nomMagazin === '—') {
        $nomMagazin = \App\Support\DepotActif::current()?->designation ?? '';
    }
@endphp
@if ($nomMagazin !== '')
    <div class="print-magasin">{{ preg_match('/magazin/i', $nomMagazin) ? $nomMagazin : 'Magazin ' . $nomMagazin }}</div>
@endif
