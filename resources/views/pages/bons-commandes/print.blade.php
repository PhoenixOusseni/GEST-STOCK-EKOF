<!DOCTYPE html>
<html lang="fr">
<head>
    @include('partials.invoice-style')
    <title>Bon de commande {{ $bon->numero_bon }}</title>
</head>
<body>
    <div class="inv-toolbar no-print">
        <a href="{{ route('gestions_bons_commandes.show', $bon->id) }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button class="inv-btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
    </div>

    <div class="inv-page">
        @php
            $entete = \App\Models\Entete::first();
            $fmt = fn ($montant) => number_format($montant, 0, ',', ' ') . ' FCFA';
            $magasin = $bon->details->map(fn ($detail) => $detail->depot->designation ?? null)->filter()->unique()->join(', ');
        @endphp

        @include('partials.invoice-header', [
            'docLabel' => 'Bon de commande',
            'docNumber' => $bon->numero_bon,
            'statusLabel' => $bon->statut === 'valide' ? 'Validé' : 'En cours',
            'statusClass' => $bon->statut === 'valide' ? 'inv-status-paid' : 'inv-status-partial',
        ])

        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-label">Client</div>
                <div class="inv-party-name">{{ $bon->client ? ($bon->client->raison_sociale ?: $bon->client->nom) : 'Client au comptant' }}</div>
                @if ($bon->client?->telephone)
                    <div class="inv-party-line">Tél. {{ $bon->client->telephone }}</div>
                @endif
            </div>
            <div class="inv-party">
                <div class="inv-party-label">Magazin</div>
                <div class="inv-party-name">{{ $magasin ?: '—' }}</div>
                <div class="inv-party-line">{{ $bon->type_vente === 'gros' ? 'Vente en gros' : 'Vente au détail' }}</div>
            </div>
        </div>

        <div class="inv-body">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="width: 8%;" class="inv-center">#</th>
                        <th style="width: 16%;">Code</th>
                        <th>Désignation</th>
                        <th style="width: 12%;" class="inv-center">Qté</th>
                        <th style="width: 18%;" class="inv-num">Prix unit.</th>
                        <th style="width: 18%;" class="inv-num">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bon->details as $index => $detail)
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

            <div class="inv-totals-wrap">
                <div class="inv-totals">
                    <div class="inv-totals-grand">
                        <span class="lbl">Total</span>
                        <span class="val">{{ $fmt($bon->montant_total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="inv-signatures">
            <div class="inv-signature">
                <div class="cap">Le vendeur</div>
                <div class="who">{{ $bon->nomAuteur() }}</div>
                <div class="line"></div>
            </div>
            <div class="inv-signature">
                <div class="cap">Le client</div>
                <div class="line"></div>
            </div>
        </div>

        @include('partials.invoice-footer', ['footerMessage' => 'Ce bon de commande ne modifie pas le stock du magazin.'])
    </div>
</body>
</html>
