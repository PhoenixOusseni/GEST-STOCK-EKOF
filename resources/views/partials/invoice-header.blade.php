@php
    $entete = $entete ?? \App\Models\Entete::first();
@endphp
<div class="inv-accent-bar"></div>
<div class="inv-header">
    <div class="inv-brand-block">
        @if ($entete && $entete->logo)
            <div class="inv-logo">
                <img src="{{ asset('storage/' . $entete->logo) }}" alt="Logo">
            </div>
        @endif
        <div>
            <div class="inv-company-name">{{ $entete->titre ?? 'SAFRECO-GSM' }}</div>
            @if ($entete && $entete->sous_titre)
                <div class="inv-company-sub">{{ $entete->sous_titre }}</div>
            @endif
            <div class="inv-company-contact">
                @if ($entete && $entete->adresse)
                    <span>{{ $entete->adresse }}</span>
                @endif
                @if ($entete && $entete->telephone)
                    <span>Tél. {{ $entete->telephone }}</span>
                @endif
                @if ($entete && $entete->email)
                    <span>{{ $entete->email }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="inv-doc-block">
        <div class="inv-doc-kicker">Document</div>
        <div class="inv-doc-title">{{ $docLabel }}</div>
        <div class="inv-doc-number"><span>N°</span>{{ $docNumber }}</div>
        <div class="inv-doc-date">{{ $docDate }}</div>
        @isset($statusLabel)
            <div class="inv-status-pill {{ $statusClass ?? 'inv-status-paid' }}">{{ $statusLabel }}</div>
        @endisset
    </div>
</div>
<div class="inv-letter-rule"></div>
