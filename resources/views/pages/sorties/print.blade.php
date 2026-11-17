<!DOCTYPE html>
<html lang="fr">

<head>
    @include('partials.invoice-style')
</head>

<body>
    <div class="inv-toolbar no-print">
        <button class="inv-btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
        <a href="{{ route('gestions_sorties.show', $sortie->id) }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="inv-page">
        @php
            $entete = \App\Models\Entete::first();
        @endphp

        @include('partials.invoice-header', [
            'docLabel' => 'Bon de Sortie',
            'docNumber' => '#' . str_pad($sortie->id, 6, '0', STR_PAD_LEFT),
            'docDate' => \Carbon\Carbon::parse($sortie->created_at)->format('d/m/Y à H:i'),
        ])

        <!-- Client -->
        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-label">Client</div>
                <div class="inv-party-name">{{ $sortie->client?->nom ?: $sortie->client?->raison_sociale ?: 'N/A' }}</div>
                @if ($sortie->client?->telephone)
                    <div class="inv-party-line">☎ {{ $sortie->client->telephone }}</div>
                @endif
            </div>
            <div class="inv-party">
                <div class="inv-party-label">Détails</div>
                <div class="inv-party-line">N° Sortie : {{ $sortie->numero_sortie }}</div>
                <div class="inv-party-line">N° Facture : {{ $sortie->numero_facture ?? '-' }}</div>
                <div class="inv-party-line">Date de sortie : {{ $sortie->date_sortie->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Articles -->
        <div class="inv-body">
            @if ($sortie->details && count($sortie->details) > 0)
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width: 32%;">Article</th>
                            <th style="width: 16%;">Magazin</th>
                            <th style="width: 14%;" class="inv-center">Quantité</th>
                            <th style="width: 19%;" class="inv-num">Prix Unit.</th>
                            <th style="width: 19%;" class="inv-num">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sortie->details as $detail)
                            <tr>
                                <td>{{ $detail->article->designation ?? 'N/A' }}</td>
                                <td class="inv-muted-cell">{{ $detail->depot->designation ?? 'N/A' }}</td>
                                <td class="inv-center">{{ number_format($detail->quantite, 0, ',', ' ') }}</td>
                                <td class="inv-num">{{ number_format($detail->prix_vente, 0, ',', ' ') }} F</td>
                                <td class="inv-num" style="font-weight: 700;">{{ number_format($detail->prix_total, 0, ',', ' ') }} F</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="inv-totals-wrap">
                <div class="inv-totals">
                    <div class="inv-totals-row">
                        <span>Nombre d'articles</span>
                        <span>{{ $sortie->details->count() }}</span>
                    </div>
                    <div class="inv-totals-row">
                        <span>Quantité totale</span>
                        <span>{{ number_format($sortie->details->sum('quantite'), 0, ',', ' ') }}</span>
                    </div>
                    <div class="inv-totals-grand">
                        <span class="lbl">Total</span>
                        <span class="val">{{ number_format($sortie->montant_total, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="inv-signatures">
            <div class="inv-signature">
                <div class="line"></div>
                <div class="cap">Signature Responsable</div>
            </div>
            <div class="inv-signature">
                <div class="line"></div>
                <div class="cap">Signature Réceptionnaire</div>
            </div>
        </div>

        @include('partials.invoice-footer', ['footerMessage' => 'Merci de conserver ce bon à titre de justificatif.'])
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#print') {
                window.print();
            }
        });
    </script>
</body>

</html>
