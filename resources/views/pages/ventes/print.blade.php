<!DOCTYPE html>
<html lang="fr">

<head>
    @include('partials.invoice-style')
    <title>Facture {{ $vente->numero_vente }}</title>
</head>

<body>
    <div class="inv-toolbar no-print">
        <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button class="inv-btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
    </div>

    <div class="inv-page">
        @php
            $entete = \App\Models\Entete::first();
            $estSolde = ($vente->statut ?? 'non_solde') === 'solde';
            $magazins = $vente->details
                ->map(fn ($detail) => $detail->depot->designation ?? null)
                ->filter()
                ->unique()
                ->values();
            $magasinLabel = $magazins->count() > 1 ? $magazins->join(', ') : ($magazins->first() ?? '—');
            $fmt = fn ($montant) => number_format($montant, 0, ',', ' ') . ' FCFA';
        @endphp

        @include('partials.invoice-header', [
            'docLabel' => 'Facture',
            'docNumber' => $vente->numero_vente,
            'docDate' => 'Du ' . $vente->date_vente->format('d/m/Y'),
            'statusLabel' => $estSolde ? 'Soldée' : 'Non soldée',
            'statusClass' => $estSolde ? 'inv-status-paid' : 'inv-status-partial',
        ])

        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-label">Facturé à</div>
                <div class="inv-party-name">{{ $vente->client ? ($vente->client->raison_sociale ?: $vente->client->nom) : 'Client au comptant' }}</div>
                @if ($vente->client?->code)
                    <div class="inv-party-line">Code {{ $vente->client->code }}</div>
                @endif
                @if ($vente->client?->telephone)
                    <div class="inv-party-line">Tél. {{ $vente->client->telephone }}</div>
                @endif
            </div>
            <div class="inv-party">
                <div class="inv-party-label">Livraison</div>
                <div class="inv-party-name">{{ $magasinLabel }}</div>
                @if ($vente->numero_vehicule)
                    <div class="inv-party-line">Véhicule {{ $vente->numero_vehicule }}</div>
                @endif
                @if ($vente->chauffeur)
                    <div class="inv-party-line">Chauffeur {{ $vente->chauffeur }}</div>
                @endif
                @if ($vente->type_vente)
                    <div class="inv-party-line">{{ $vente->type_vente === 'gros' ? 'Vente en gros' : 'Vente au détail' }}</div>
                @endif
            </div>
        </div>

        <div class="inv-meta">
            <div class="inv-meta-item">
                <div class="inv-meta-label">Date</div>
                <div class="inv-meta-value">{{ $vente->date_vente->format('d/m/Y') }}</div>
            </div>
            <div class="inv-meta-item">
                <div class="inv-meta-label">Magazin</div>
                <div class="inv-meta-value">{{ $magasinLabel }}</div>
            </div>
            <div class="inv-meta-item">
                <div class="inv-meta-label">Lignes</div>
                <div class="inv-meta-value">{{ $vente->details->count() }}</div>
            </div>
            <div class="inv-meta-item">
                <div class="inv-meta-label">Quantité</div>
                <div class="inv-meta-value">{{ number_format($vente->details->sum('quantite'), 0, ',', ' ') }}</div>
            </div>
        </div>

        <div class="inv-body">
            @if ($vente->details && count($vente->details) > 0)
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width: 6%;" class="inv-center">#</th>
                            <th style="width: 14%;">Code</th>
                            <th>Désignation</th>
                            <th style="width: 10%;" class="inv-center">Qté</th>
                            <th style="width: 18%;" class="inv-num">Prix unit.</th>
                            <th style="width: 18%;" class="inv-num">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vente->details as $index => $detail)
                            <tr>
                                <td class="inv-center inv-muted-cell">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="inv-muted-cell">{{ $detail->article->code ?? '—' }}</td>
                                <td class="inv-designation">{{ $detail->article->designation ?? '—' }}</td>
                                <td class="inv-center">{{ number_format($detail->quantite, 0, ',', ' ') }}</td>
                                <td class="inv-num">{{ $fmt($detail->prix_vente) }}</td>
                                <td class="inv-num inv-designation">{{ $fmt($detail->prix_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="inv-totals-wrap">
                <div class="inv-totals">
                    <div class="inv-totals-row">
                        <span>Total facture</span>
                        <span>{{ $fmt($vente->montant_total) }}</span>
                    </div>
                    <div class="inv-totals-row strong">
                        <span>Montant encaissé</span>
                        <span>{{ $fmt($vente->montant_paye) }}</span>
                    </div>
                    <div class="inv-totals-row {{ $vente->reste_a_payer > 0 ? 'due' : '' }}">
                        <span>Reste à payer</span>
                        <span>{{ $fmt($vente->reste_a_payer) }}</span>
                    </div>
                    <div class="inv-totals-grand">
                        <span class="lbl">Net à payer</span>
                        <span class="val">{{ $fmt($vente->montant_total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="inv-signatures">
            <div class="inv-signature">
                <div class="cap">Le vendeur</div>
                <div class="line"></div>
            </div>
            <div class="inv-signature">
                <div class="cap">Le client</div>
                <div class="line"></div>
            </div>
        </div>

        @include('partials.invoice-footer', ['footerMessage' => 'Merci pour votre confiance. Cette facture tient lieu de justificatif.'])
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
