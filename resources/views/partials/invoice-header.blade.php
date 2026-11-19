@php
    $entete = $entete ?? \App\Models\Entete::first();
@endphp
<div class="inv-header">
    <div class="inv-watermark" aria-hidden="true">
        <svg viewBox="0 0 261 106" preserveAspectRatio="xMidYMid meet">
            <defs>
                <filter id="doc-filigrane" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
                    <feColorMatrix type="matrix" values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  -1 -1 -1 0 2.78"/>
                </filter>
            </defs>
            <image href="{{ asset('images/logo.png') }}" width="261" height="106" filter="url(#doc-filigrane)" opacity="0.34"/>
        </svg>
    </div>
    <div class="inv-header-inner">
        <div class="inv-brand-block">
            <div class="inv-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EKDF eau Bisimilah">
            </div>
            <div>
                <div class="inv-company-name">{{ $entete->titre ?? 'SAFRECO-GSM' }}</div>
                @if ($entete && $entete->sous_titre)
                    <div class="inv-company-sub">{{ $entete->sous_titre }}</div>
                @endif
                <div class="inv-company-contact">
                    @if ($entete && $entete->adresse)
                        <span>{{ $entete->adresse }}</span>
                    @endif
                    @if ($entete && $entete->email)
                        <span>{{ $entete->email }}</span>
                    @endif
                    @include('partials.print-phones')
                    @include('partials.print-magasin')
                </div>
            </div>
        </div>

        <div class="inv-doc-block">
            <div class="inv-doc-kicker">Document</div>
            <div class="inv-doc-title">{{ $docLabel }}</div>
            <div class="inv-doc-number"><span>N°</span>{{ $docNumber }}</div>
            @isset($docDate)
                <div class="inv-doc-date">{{ $docDate }}</div>
            @endisset
            @isset($auteur)
                <div class="inv-doc-author">Par {{ $auteur }}</div>
            @endisset
            @isset($statusLabel)
                <div class="inv-status-pill {{ $statusClass ?? 'inv-status-paid' }}">{{ $statusLabel }}</div>
            @endisset
        </div>
    </div>
</div>
