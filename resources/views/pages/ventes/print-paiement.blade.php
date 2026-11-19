<!DOCTYPE html>
<html lang="fr">
<head>
    @include('partials.invoice-style')
    <title>Reçu de paiement — {{ $vente->numero_vente }}</title>
</head>
<body>
    @php
        $entete = \App\Models\Entete::first();
        $fmt = fn ($montant) => number_format($montant, 0, ',', ' ') . ' FCFA';
        $cumulAvant = 0;
        foreach ($vente->paiements as $p) {
            if ($p->id == $paiement->id) {
                break;
            }
            $cumulAvant += $p->montant;
        }
        $resteApres = max(0, $vente->montant_total - ($cumulAvant + $paiement->montant));
    @endphp

    <div class="inv-toolbar no-print">
        <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <button class="inv-btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
    </div>

    <div class="inv-page">
        @include('partials.invoice-header', [
            'docLabel' => 'Reçu de paiement',
            'docNumber' => $vente->numero_vente,
            'docDate' => 'Du ' . $paiement->date_paiement->format('d/m/Y'),
            'auteur' => $vente->nomAuteur(),
            'statusLabel' => $resteApres <= 0 ? 'Soldé' : 'Non soldé',
            'statusClass' => $resteApres <= 0 ? 'inv-status-paid' : 'inv-status-partial',
        ])

        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-label">Client</div>
                <div class="inv-party-name">{{ $vente->client ? ($vente->client->raison_sociale ?: $vente->client->nom) : 'Sans client' }}</div>
                @if ($vente->client?->telephone)
                    <div class="inv-party-line">Tél. {{ $vente->client->telephone }}</div>
                @endif
            </div>
            <div class="inv-party">
                <div class="inv-party-label">Versement</div>
                <div class="inv-party-name">{{ $paiement->mode_paiement ?: 'Espèces' }}</div>
                <div class="inv-party-line">Facture {{ $vente->numero_vente }}</div>
                @if ($paiement->notes)
                    <div class="inv-party-line">{{ $paiement->notes }}</div>
                @endif
            </div>
        </div>

        <div class="inv-body">
            <div class="inv-totals-wrap" style="justify-content: stretch;">
                <div class="inv-totals" style="width: 100%;">
                    <div class="inv-totals-row">
                        <span>Montant de la facture</span>
                        <span>{{ $fmt($vente->montant_total) }}</span>
                    </div>
                    <div class="inv-totals-row">
                        <span>Déjà payé avant ce versement</span>
                        <span>{{ $fmt($cumulAvant) }}</span>
                    </div>
                    <div class="inv-totals-row strong">
                        <span>Montant de ce versement</span>
                        <span>{{ $fmt($paiement->montant) }}</span>
                    </div>
                    <div class="inv-totals-row {{ $resteApres > 0 ? 'due' : '' }}">
                        <span>Reste à payer</span>
                        <span>{{ $fmt($resteApres) }}</span>
                    </div>
                    <div class="inv-totals-grand">
                        <span class="lbl">Versement</span>
                        <span class="val">{{ $fmt($paiement->montant) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="inv-signatures">
            <div class="inv-signature">
                <div class="cap">Le caissier</div>
                <div class="line"></div>
            </div>
            <div class="inv-signature">
                <div class="cap">Le client</div>
                <div class="line"></div>
            </div>
        </div>

        @include('partials.invoice-footer', ['footerMessage' => 'Reçu à conserver avec la facture.'])
    </div>
</body>
</html>
