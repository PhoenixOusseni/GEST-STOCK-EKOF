<!DOCTYPE html>
<html lang="fr">

<head>
    @include('pages.encaissements.style')
</head>

<body>
    <div class="print-container">
        <!-- Boutons d'impression (cachés à l'impression) -->
        <div class="print-buttons no-print">
            <button class="btn-print" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimer
            </button>
            <a href="{{ route('gestions_decaissements.show', $decaissement->id) }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @php
            $entete = \App\Models\Entete::first();
        @endphp

        <!-- En-tête personnalisé avec infos de la table entete -->
        <div class="header">
            @include('partials.print-watermark')
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EKDF eau Bisimilah">
            </div>

            <div class="header-info">
                <div class="header-title">{{ $entete->titre ?? 'SAFRECO-GSM' }}</div>
                @if ($entete && $entete->sous_titre)
                    <div class="header-subtitle">{{ $entete->sous_titre }}</div>
                @endif
                <div class="header-contact">
                    @if ($entete && $entete->telephone)
                        <div class="header-contact-item">
                            <span>☎ {{ $entete->telephone }}</span>
                        </div>
                    @endif
                    @if ($entete && $entete->email)
                        <div class="header-contact-item">
                            <span>✉ {{ $entete->email }}</span>
                        </div>
                    @endif
                    @if ($entete && $entete->adresse)
                        <div class="header-contact-item">
                            <span>📍 {{ $entete->adresse }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="header-receipt">
                <div class="header-receipt-no">BON DE DÉCAISSEMENT</div>
                <div class="header-receipt-id">#{{ str_pad($decaissement->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="receipt-date">{{ \Carbon\Carbon::parse($decaissement->created_at)->format('d/m/Y à H:i') }}
                </div>
                <div class="receipt-author">Par {{ $decaissement->nomAuteur() }}</div>
            </div>
        </div>

        <!-- Informations de l'achat -->
        <div class="info-section">
            <div class="two-columns">
                <div class="column">
                    <div class="info-row">
                        <span class="info-label">N° Achat :</span>
                        <span class="info-value"><strong>{{ $decaissement->achat->numero_achat }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mode Paiement :</span>
                        <span class="badge badge-primary">{{ $decaissement->mode_paiement }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="info-row">
                        <span class="info-label">Fournisseur :</span>
                        <span
                            class="info-value"><strong>{{ $decaissement->achat->fournisseur->nom ?? 'N/A' }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date Achat :</span>
                        <span class="info-value">{{ $decaissement->achat->date_achat->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Informations du décaissement -->
        <div class="info-section">
            <div class="two-columns">
                <div class="column">
                    <div class="info-row">
                        <span class="info-label">Montant Total :</span>
                        <span class="info-value">{{ number_format($decaissement->montant, 0, ',', ' ') }}
                            FCFA</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Reste à Décaisser :</span>
                        <span class="info-value">
                            <span
                                class="badge badge-primary">{{ number_format($decaissement->reste, 0, ',', ' ') }}
                                FCFA</span>
                        </span>
                    </div>
                </div>
                <div class="column">
                    <div class="info-row">
                        <span class="info-label">Montant Décaissé :</span>
                        <span
                            class="info-value"><strong>{{ number_format($decaissement->montant_decaisse, 0, ',', ' ') }} FCFA</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pourcentage :</span>
                        <span class="info-value">
                            <span
                                class="badge badge-primary">{{ number_format(($decaissement->montant_decaisse / $decaissement->montant) * 100, 0, ',', ' ') }}%</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau articles de l'achat -->
        @if ($decaissement->achat->details && count($decaissement->achat->details) > 0)
            <div style="margin-top: 25px;">
                <div class="info-section-title">Détail des Articles</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Article</th>
                            <th style="width: 15%; text-align: center;">Quantité</th>
                            <th style="width: 20%; text-align: right;">Prix Unitaire</th>
                            <th style="width: 30%; text-align: right;">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($decaissement->achat->details as $detail)
                            <tr>
                                <td>{{ $detail->article->designation ?? 'N/A' }}</td>
                                <td style="text-align: center;">{{ $detail->quantite }}</td>
                                <td style="text-align: right;">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }}
                                    FCFA</td>
                                <td style="text-align: right; font-weight: 600;">
                                    {{ number_format($detail->prix_total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Résumé des montants -->
        <div class="amount-section">
            <div class="amount-row">
                <span class="amount-label">Montant Total du Achat :</span>
                <span
                    class="amount-value">{{ number_format($decaissement->achat->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Montant Décaissé :</span>
                <span
                    class="amount-value">{{ number_format($decaissement->montant_decaisse, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row total">
                <span class="amount-label">Reste à Décaisser :</span>
                <span class="amount-value">{{ number_format($decaissement->reste, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <!-- Section Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <strong>Signature Trésorier(e)</strong>
                <div style="margin-top: 20px;">_________________</div>
            </div>
            <div class="signature-box">
                <strong>Signature Fournisseur</strong>
                <div style="margin-top: 20px;">_________________</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>
                Ce bon de décaissement a été généré le {{ now()->format('d/m/Y à H:i:s') }} — {{ $entete->titre ?? 'EKDF' }}.
            </p>
            <p style="margin-top: 10px;">
                <strong>À conserver à titre de justificatif de paiement.</strong>
            </p>
        </div>
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
