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
        <a href="{{ route('gestions_entrees.show', $entree->id) }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="inv-page">
        @php
            $entete = \App\Models\Entete::first();
            $magasin = $entree->details->map(fn ($detail) => $detail->depot->designation ?? null)->filter()->unique()->join(', ');
        @endphp

        @include('partials.invoice-header', [
            'docLabel' => "Bon d'Entrée",
            'docNumber' => '#' . str_pad($entree->id, 6, '0', STR_PAD_LEFT),
            'docDate' => \Carbon\Carbon::parse($entree->created_at)->format('d/m/Y à H:i'),
            'auteur' => $entree->nomAuteur(),
        ])

        <!-- Fournisseur -->
        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-label">Fournisseur</div>
                <div class="inv-party-name">{{ $entree->fournisseur?->raison_sociale ?: $entree->fournisseur?->nom ?: 'N/A' }}</div>
                @if ($entree->fournisseur?->telephone)
                    <div class="inv-party-line">☎ {{ $entree->fournisseur->telephone }}</div>
                @endif
            </div>
            <div class="inv-party">
                <div class="inv-party-label">Détails</div>
                <div class="inv-party-line">N° Entrée : {{ $entree->numero_entree }}</div>
                <div class="inv-party-line">N° Facture : {{ $entree->numero_facture ?? '-' }}</div>
                <div class="inv-party-line">Date d'entrée : {{ $entree->date_entree->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Articles -->
        <div class="inv-body">
            @if ($entree->details && count($entree->details) > 0)
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Article</th>
                            <th style="width: 18%;" class="inv-center">Quantité</th>
                            <th style="width: 21%;" class="inv-num">Prix Unit.</th>
                            <th style="width: 21%;" class="inv-num">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entree->details as $detail)
                            <tr>
                                <td>{{ $detail->article->designation ?? 'N/A' }}</td>
                                <td class="inv-center">{{ number_format($detail->stock, 0, ',', ' ') }}</td>
                                <td class="inv-num">{{ number_format($detail->prix_achat, 0, ',', ' ') }} F</td>
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
                        <span>{{ $entree->details->count() }}</span>
                    </div>
                    <div class="inv-totals-row">
                        <span>Quantité totale</span>
                        <span>{{ number_format($entree->details->sum('stock'), 0, ',', ' ') }}</span>
                    </div>
                    <div class="inv-totals-grand">
                        <span class="lbl">Total</span>
                        <span class="val">{{ number_format($entree->montant_total, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="inv-signatures">
            <div class="inv-signature">
                <div class="line"></div>
                <div class="cap">Signature Fournisseur</div>
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
