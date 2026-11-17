<!DOCTYPE html>
<html lang="fr">

<head>
    @include('partials.invoice-style')
    <title>Facture d'achat {{ $achat->numero_achat }}</title>
</head>

<body>
    <div class="inv-toolbar no-print">
        <a href="{{ route('gestions_achats.show', $achat->id) }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button class="inv-btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
    </div>

    <div class="inv-page">
        @php
            $entete = \App\Models\Entete::first();
            $estSolde = ($achat->statut ?? 'non_solde') === 'solde';
            $magazins = $achat->details
                ->map(fn ($detail) => $detail->depot->designation ?? null)
                ->filter()
                ->unique()
                ->values();
            $magasinLabel = $magazins->count() > 1 ? $magazins->join(', ') : ($magazins->first() ?? '—');
            $fmt = fn ($montant) => number_format($montant, 0, ',', ' ') . ' FCFA';
        @endphp

        @include('partials.invoice-header', [
            'docLabel' => "Facture d'achat",
            'docNumber' => $achat->numero_achat,
            'docDate' => 'Du ' . $achat->date_achat->format('d/m/Y'),
            'statusLabel' => $estSolde ? 'Soldée' : 'Non soldée',
            'statusClass' => $estSolde ? 'inv-status-paid' : 'inv-status-partial',
        ])

        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-label">Fournisseur</div>
                <div class="inv-party-name">{{ $achat->fournisseur?->raison_sociale ?: $achat->fournisseur?->nom ?: 'Fournisseur' }}</div>
                @if ($achat->fournisseur?->telephone)
                    <div class="inv-party-line">Tél. {{ $achat->fournisseur->telephone }}</div>
                @endif
                @if ($achat->fournisseur?->email)
                    <div class="inv-party-line">{{ $achat->fournisseur->email }}</div>
                @endif
                @if ($achat->fournisseur?->adresse)
                    <div class="inv-party-line">{{ $achat->fournisseur->adresse }}</div>
                @endif
            </div>
            <div class="inv-party">
                <div class="inv-party-label">Réception</div>
                <div class="inv-party-name">{{ $magasinLabel }}</div>
                @if ($achat->designation)
                    <div class="inv-party-line">{{ $achat->designation }}</div>
                @endif
            </div>
        </div>

        <div class="inv-meta">
            <div class="inv-meta-item">
                <div class="inv-meta-label">Date</div>
                <div class="inv-meta-value">{{ $achat->date_achat->format('d/m/Y') }}</div>
            </div>
            <div class="inv-meta-item">
                <div class="inv-meta-label">Magazin</div>
                <div class="inv-meta-value">{{ $magasinLabel }}</div>
            </div>
            <div class="inv-meta-item">
                <div class="inv-meta-label">Lignes</div>
                <div class="inv-meta-value">{{ $achat->details->count() }}</div>
            </div>
            <div class="inv-meta-item">
                <div class="inv-meta-label">Quantité</div>
                <div class="inv-meta-value">{{ number_format($achat->details->sum('quantite'), 2, ',', ' ') }}</div>
            </div>
        </div>

        <div class="inv-body">
            @if ($achat->details && count($achat->details) > 0)
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
                        @foreach ($achat->details as $index => $detail)
                            <tr>
                                <td class="inv-center inv-muted-cell">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="inv-muted-cell">{{ $detail->article->code ?? '—' }}</td>
                                <td class="inv-designation">{{ $detail->article->designation ?? '—' }}</td>
                                <td class="inv-center">{{ number_format($detail->quantite, 2, ',', ' ') }}</td>
                                <td class="inv-num">{{ $fmt($detail->prix_unitaire) }}</td>
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
                        <span>{{ $fmt($achat->montant_total) }}</span>
                    </div>
                    <div class="inv-totals-row strong">
                        <span>Montant réglé</span>
                        <span>{{ $fmt($achat->montant_paye) }}</span>
                    </div>
                    <div class="inv-totals-row {{ $achat->reste_a_payer > 0 ? 'due' : '' }}">
                        <span>Reste à payer</span>
                        <span>{{ $fmt($achat->reste_a_payer) }}</span>
                    </div>
                    <div class="inv-totals-grand">
                        <span class="lbl">Net à payer</span>
                        <span class="val">{{ $fmt($achat->montant_total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="inv-signatures">
            <div class="inv-signature">
                <div class="cap">Le responsable</div>
                <div class="line"></div>
            </div>
            <div class="inv-signature">
                <div class="cap">Le fournisseur</div>
                <div class="line"></div>
            </div>
        </div>

        @include('partials.invoice-footer', ['footerMessage' => 'Document à conserver avec les pièces d\'achat.'])
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
