<!DOCTYPE html>
<html lang="fr">

<head>
    @include('pages.encaissements.style')
    <title>État Caisse {{ $caisse->code }}</title>
    <style>
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 20px 0;
        }

        .summary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 14px 10px;
            text-align: center;
            background: #f8f9fa;
        }

        .summary-card .sc-value {
            font-size: 17px;
            font-weight: 700;
            color: #0d47a1;
        }

        .summary-card .sc-label {
            font-size: 10px;
            color: #777;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .summary-card.encaisse .sc-value  { color: #198754; }
        .summary-card.decaisse .sc-value  { color: #dc3545; }
        .summary-card.recouvrir .sc-value { color: #fd7e14; }
        .summary-card.payer .sc-value     { color: #dc3545; }
        .summary-card.solde-positif .sc-value { color: #198754; }
        .summary-card.solde-negatif .sc-value { color: #dc3545; }

        .section-title {
            background-color: #f8f9fa;
            border-left: 5px solid #1565c0;
            padding: 8px 14px;
            font-weight: 700;
            font-size: 12px;
            margin: 18px 0 10px 0;
            text-transform: uppercase;
            color: #0d47a1;
            page-break-after: avoid;
        }

        .section-title.encaiss { border-left-color: #198754; }
        .section-title.decaiss { border-left-color: #dc3545; }

        .text-success-p { color: #198754; font-weight: 700; }
        .text-danger-p  { color: #dc3545; font-weight: 700; }
        .text-warn-p    { color: #fd7e14; font-weight: 700; }
        .text-muted-p   { color: #6c757d; }

        .solde-final-box {
            margin: 20px 0;
            padding: 18px 24px;
            border: 2px solid #1565c0;
            border-radius: 5px;
            background: linear-gradient(135deg, #f0f7f0 0%, #fff 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .solde-final-label {
            font-size: 15px;
            font-weight: 700;
            color: #0d47a1;
            text-transform: uppercase;
        }

        .solde-final-value {
            font-size: 24px;
            font-weight: 700;
        }

        .badge-mode {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            background: #e0e7ff;
            color: #0d47a1;
        }

        .badge-partial {
            background: #fff3cd;
            color: #856404;
        }

        .badge-done {
            background: #d4edda;
            color: #155724;
        }

        @media print {
            .summary-grid { grid-template-columns: repeat(4, 1fr); }
            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; }
        }
    </style>
</head>

<body>
    <div class="print-container">

        <!-- Boutons hors impression -->
        <div class="print-buttons no-print">
            <button class="btn-print" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimer
            </button>
            <a href="{{ route('gestions_caisses.show', $caisse->id) }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @php $entete = \App\Models\Entete::first(); @endphp

        <!-- En-tête -->
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
                    @if ($entete && $entete->email)
                        <div class="header-contact-item"><span>&#9993; {{ $entete->email }}</span></div>
                    @endif
                    @if ($entete && $entete->adresse)
                        <div class="header-contact-item"><span>&#128205; {{ $entete->adresse }}</span></div>
                    @endif
                </div>
                @include('partials.print-phones')
                @include('partials.print-magasin')
            </div>

            <div class="header-receipt">
                <div class="header-receipt-no">ÉTAT DE CAISSE</div>
                <div class="header-receipt-id">{{ $caisse->code }}</div>
                <div class="receipt-date">Édité le {{ now()->format('d/m/Y à H:i') }}</div>
            </div>
        </div>

        <!-- Informations de la caisse -->
        <div class="info-section">
            <div class="two-columns">
                <div class="column">
                    <div class="info-section-title">Identification de la Caisse</div>
                    <div class="info-row">
                        <span class="info-label">Code :</span>
                        <span class="info-value"><strong>{{ $caisse->code }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">N° de Compte :</span>
                        <span class="info-value"><strong>{{ $caisse->numero_compte }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dénomination :</span>
                        <span class="info-value">{{ $caisse->designation }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Téléphone :</span>
                        <span class="info-value">{{ $caisse->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email :</span>
                        <span class="info-value">{{ $caisse->email ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Adresse :</span>
                        <span class="info-value">{{ $caisse->adresse ?? 'Non renseignée' }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="info-section-title">Récapitulatif Financier</div>
                    <div class="info-row">
                        <span class="info-label">Total Encaissé :</span>
                        <span class="info-value text-success-p">{{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Décaissé :</span>
                        <span class="info-value text-danger-p">{{ number_format($totalDecaisse, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Solde à Recouvrir :</span>
                        <span class="info-value text-warn-p">{{ number_format($totalResteEncaiss, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Solde à Payer :</span>
                        <span class="info-value text-danger-p">{{ number_format($totalResteDecaiss, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nb. Encaissements :</span>
                        <span class="info-value">{{ $nbEncaissements }} (dont {{ $nbEncaissPartiel }} partiel(s))</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nb. Décaissements :</span>
                        <span class="info-value">{{ $nbDecaissements }} (dont {{ $nbDecaissPartiel }} partiel(s))</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes de synthèse -->
        <div class="summary-grid">
            <div class="summary-card encaisse">
                <div class="sc-value">{{ number_format($totalEncaisse, 0, ',', ' ') }}</div>
                <div class="sc-label">Total Encaissé (FCFA)</div>
            </div>
            <div class="summary-card decaisse">
                <div class="sc-value">{{ number_format($totalDecaisse, 0, ',', ' ') }}</div>
                <div class="sc-label">Total Décaissé (FCFA)</div>
            </div>
            <div class="summary-card recouvrir">
                <div class="sc-value">{{ number_format($totalResteEncaiss, 0, ',', ' ') }}</div>
                <div class="sc-label">À Recouvrir (FCFA)</div>
            </div>
            <div class="summary-card payer">
                <div class="sc-value">{{ number_format($totalResteDecaiss, 0, ',', ' ') }}</div>
                <div class="sc-label">Reste à Payer (FCFA)</div>
            </div>
        </div>

        <!-- Solde final -->
        <div class="solde-final-box">
            <div class="solde-final-label">Solde Actuel de la Caisse</div>
            <div class="solde-final-value {{ $caisse->solde >= 0 ? 'text-success-p' : 'text-danger-p' }}">
                {{ number_format($caisse->solde, 0, ',', ' ') }} FCFA
                @if ($caisse->solde < 0)
                    <span style="font-size: 13px; font-weight: 400;">(déficitaire)</span>
                @endif
            </div>
        </div>

        <!-- ===== ENCAISSEMENTS ===== -->
        <div class="section-title encaiss">Détail des Encaissements ({{ $nbEncaissements }})</div>

        @if ($encaissements->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:5%; text-align:center;">#</th>
                        <th style="width:12%;">Date</th>
                        <th style="width:12%;">N° Vente</th>
                        <th style="width:22%;">Client</th>
                        <th style="width:13%; text-align:right;">Montant Total</th>
                        <th style="width:13%; text-align:right;">Encaissé</th>
                        <th style="width:13%; text-align:right;">Reste</th>
                        <th style="width:10%; text-align:center;">Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($encaissements as $index => $enc)
                        <tr>
                            <td style="text-align:center;">{{ $index + 1 }}</td>
                            <td>{{ $enc->date_encaissement->format('d/m/Y') }}</td>
                            <td><strong>{{ $enc->vente->numero_vente ?? 'N/A' }}</strong></td>
                            <td>{{ $enc->vente->client->raison_sociale ?? ($enc->vente->client->nom ?? 'N/A') }}</td>
                            <td style="text-align:right;">{{ number_format($enc->montant, 0, ',', ' ') }}</td>
                            <td style="text-align:right;" class="text-success-p">{{ number_format($enc->montant_encaisse, 0, ',', ' ') }}</td>
                            <td style="text-align:right;" class="{{ $enc->reste > 0 ? 'text-warn-p' : 'text-muted-p' }}">{{ number_format($enc->reste, 0, ',', ' ') }}</td>
                            <td style="text-align:center;">
                                <span class="badge-mode">{{ $enc->mode_paiement ?? 'N/A' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8f9fa; font-weight:700; border-top:2px solid #333;">
                        <td colspan="4" style="text-align:right; padding:10px;">TOTAUX :</td>
                        <td style="text-align:right; padding:10px;">{{ number_format($encaissements->sum('montant'), 0, ',', ' ') }}</td>
                        <td style="text-align:right; padding:10px;" class="text-success-p">{{ number_format($totalEncaisse, 0, ',', ' ') }}</td>
                        <td style="text-align:right; padding:10px;" class="text-warn-p">{{ number_format($totalResteEncaiss, 0, ',', ' ') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Résumé encaissements -->
            <div class="amount-section" style="margin-bottom: 20px;">
                <div class="amount-row">
                    <span class="amount-label">Encaissements soldés (reste = 0)</span>
                    <span class="amount-value">{{ $encaissements->where('reste', 0)->count() }} / {{ $nbEncaissements }}</span>
                </div>
                <div class="amount-row">
                    <span class="amount-label">Encaissements partiels (reste > 0)</span>
                    <span class="amount-value text-warn-p">{{ $nbEncaissPartiel }}</span>
                </div>
                <div class="amount-row">
                    <span class="amount-label">Solde total à recouvrir (créances clients)</span>
                    <span class="amount-value text-warn-p">{{ number_format($totalResteEncaiss, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="amount-row total">
                    <span class="amount-label">Total encaissé</span>
                    <span class="amount-value text-success-p">{{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        @else
            <p class="text-muted-p" style="font-style: italic; padding: 10px 0;">Aucun encaissement enregistré pour cette caisse.</p>
        @endif

        <!-- ===== DÉCAISSEMENTS ===== -->
        <div class="section-title decaiss">Détail des Décaissements ({{ $nbDecaissements }})</div>

        @if ($decaissements->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:5%; text-align:center;">#</th>
                        <th style="width:12%;">Date</th>
                        <th style="width:12%;">N° Achat</th>
                        <th style="width:22%;">Fournisseur</th>
                        <th style="width:13%; text-align:right;">Montant Total</th>
                        <th style="width:13%; text-align:right;">Décaissé</th>
                        <th style="width:13%; text-align:right;">Reste</th>
                        <th style="width:10%; text-align:center;">Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($decaissements as $index => $dec)
                        <tr>
                            <td style="text-align:center;">{{ $index + 1 }}</td>
                            <td>{{ $dec->date_decaissement->format('d/m/Y') }}</td>
                            <td><strong>{{ $dec->achat->numero_achat ?? 'N/A' }}</strong></td>
                            <td>{{ $dec->achat->fournisseur->nom ?? 'N/A' }}</td>
                            <td style="text-align:right;">{{ number_format($dec->montant, 0, ',', ' ') }}</td>
                            <td style="text-align:right;" class="text-danger-p">{{ number_format($dec->montant_decaisse, 0, ',', ' ') }}</td>
                            <td style="text-align:right;" class="{{ $dec->reste > 0 ? 'text-warn-p' : 'text-muted-p' }}">{{ number_format($dec->reste, 0, ',', ' ') }}</td>
                            <td style="text-align:center;">
                                <span class="badge-mode">{{ $dec->mode_paiement ?? 'N/A' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8f9fa; font-weight:700; border-top:2px solid #333;">
                        <td colspan="4" style="text-align:right; padding:10px;">TOTAUX :</td>
                        <td style="text-align:right; padding:10px;">{{ number_format($decaissements->sum('montant'), 0, ',', ' ') }}</td>
                        <td style="text-align:right; padding:10px;" class="text-danger-p">{{ number_format($totalDecaisse, 0, ',', ' ') }}</td>
                        <td style="text-align:right; padding:10px;" class="text-warn-p">{{ number_format($totalResteDecaiss, 0, ',', ' ') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Résumé décaissements -->
            <div class="amount-section" style="margin-bottom: 20px;">
                <div class="amount-row">
                    <span class="amount-label">Décaissements soldés (reste = 0)</span>
                    <span class="amount-value">{{ $decaissements->where('reste', 0)->count() }} / {{ $nbDecaissements }}</span>
                </div>
                <div class="amount-row">
                    <span class="amount-label">Décaissements partiels (reste > 0)</span>
                    <span class="amount-value text-warn-p">{{ $nbDecaissPartiel }}</span>
                </div>
                <div class="amount-row">
                    <span class="amount-label">Solde total à payer (dettes fournisseurs)</span>
                    <span class="amount-value text-warn-p">{{ number_format($totalResteDecaiss, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="amount-row total">
                    <span class="amount-label">Total décaissé</span>
                    <span class="amount-value text-danger-p">{{ number_format($totalDecaisse, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        @else
            <p class="text-muted-p" style="font-style: italic; padding: 10px 0;">Aucun décaissement enregistré pour cette caisse.</p>
        @endif

        <!-- ===== BILAN FINAL ===== -->
        <div class="section-title">Bilan Global de la Caisse</div>
        <div class="amount-section">
            <div class="amount-row">
                <span class="amount-label">Total Encaissé (entrées)</span>
                <span class="amount-value text-success-p">+ {{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Total Décaissé (sorties)</span>
                <span class="amount-value text-danger-p">- {{ number_format($totalDecaisse, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Solde à Recouvrir (créances clients non soldées)</span>
                <span class="amount-value text-warn-p">{{ number_format($totalResteEncaiss, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Reste à Payer (dettes fournisseurs non soldées)</span>
                <span class="amount-value text-warn-p">{{ number_format($totalResteDecaiss, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row total">
                <span class="amount-label">Solde Actuel de la Caisse</span>
                <span class="amount-value {{ $caisse->solde >= 0 ? 'text-success-p' : 'text-danger-p' }}">
                    {{ number_format($caisse->solde, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section" style="margin-top: 50px;">
            <div class="signature-box">
                <strong>Responsable Caisse</strong>
                <div style="margin-top: 40px;">_________________</div>
            </div>
            <div class="signature-box">
                <strong>Contrôleur</strong>
                <div style="margin-top: 40px;">_________________</div>
            </div>
            <div class="signature-box">
                <strong>Direction</strong>
                <div style="margin-top: 40px;">_________________</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>
                État de caisse <strong>{{ $caisse->designation }}</strong> ({{ $caisse->code }}) —
                Généré le {{ now()->format('d/m/Y à H:i:s') }} — {{ $entete->titre ?? 'EKDF' }}.
            </p>
            <p style="margin-top: 6px;"><strong>Document officiel — à conserver.</strong></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash === '#print') {
                window.print();
            }
        });
    </script>
</body>

</html>
