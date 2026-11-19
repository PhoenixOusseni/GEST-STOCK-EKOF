<!DOCTYPE html>
<html lang="fr">

<head>
    @include('pages.encaissements.style')
    <title>Inventaire {{ $inventaire->numero_inventaire }}</title>
    <style>
        .ecart-negatif { color: #dc3545; font-weight: 700; }
        .ecart-positif { color: #198754; font-weight: 700; }
        .ecart-neutre  { color: #6c757d; }

        .statut-valide    { background-color: #d4edda; color: #155724; padding: 4px 10px; border-radius: 5px; font-size: 12px; font-weight: 700; }
        .statut-en-cours  { background-color: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 5px; font-size: 12px; font-weight: 700; }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
        }
        .summary-card .sc-value {
            font-size: 22px;
            font-weight: 700;
            color: #0d47a1;
        }
        .summary-card .sc-label {
            font-size: 11px;
            color: #777;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .summary-card.deficit .sc-value { color: #dc3545; }
        .summary-card.surplus .sc-value { color: #198754; }
    </style>
</head>

<body>
    <div class="print-container">

        <!-- Boutons d'impression -->
        <div class="print-buttons no-print">
            <button class="btn-print" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimer
            </button>
            <a href="{{ route('gestions_inventaires.show', $inventaire->id) }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @php
            $entete = \App\Models\Entete::first();
        @endphp

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
                    @if ($entete && $entete->telephone)
                        <div class="header-contact-item"><span>&#9990; {{ $entete->telephone }}</span></div>
                    @endif
                    @if ($entete && $entete->email)
                        <div class="header-contact-item"><span>&#9993; {{ $entete->email }}</span></div>
                    @endif
                    @if ($entete && $entete->adresse)
                        <div class="header-contact-item"><span>&#128205; {{ $entete->adresse }}</span></div>
                    @endif
                </div>
            </div>

            <div class="header-receipt">
                <div class="header-receipt-no">FICHE D'INVENTAIRE</div>
                <div class="header-receipt-id">{{ $inventaire->numero_inventaire }}</div>
                <div class="receipt-date">{{ $inventaire->date_inventaire->format('d/m/Y') }}</div>
                <div class="receipt-author">Par {{ $inventaire->nomAuteur() }}</div>
            </div>
        </div>

        <!-- Informations générales -->
        <div class="info-section">
            <div class="two-columns">
                <div class="column">
                    <div class="info-section-title">Informations Inventaire</div>
                    <div class="info-row">
                        <span class="info-label">N° Inventaire :</span>
                        <span class="info-value"><strong>{{ $inventaire->numero_inventaire }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date :</span>
                        <span class="info-value">{{ $inventaire->date_inventaire->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Magazin :</span>
                        <span class="info-value"><strong>{{ $inventaire->depot->designation ?? 'N/A' }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Statut :</span>
                        <span class="info-value">
                            @if ($inventaire->statut == 'valide')
                                <span class="statut-valide">Validé</span>
                            @else
                                <span class="statut-en-cours">En cours</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="column">
                    <div class="info-section-title">Récapitulatif</div>
                    <div class="info-row">
                        <span class="info-label">Nb. d'articles :</span>
                        <span class="info-value">{{ $inventaire->details->count() }} article(s)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Écart total (Qté) :</span>
                        <span class="info-value">
                            @php $ecartTotal = $inventaire->ecart_total_quantite ?? $inventaire->details->sum('ecart_quantite'); @endphp
                            <strong class="{{ $ecartTotal < 0 ? 'ecart-negatif' : ($ecartTotal > 0 ? 'ecart-positif' : 'ecart-neutre') }}">
                                {{ $ecartTotal >= 0 ? '+' : '' }}{{ $ecartTotal }}
                            </strong>
                        </span>
                    </div>
                    @if ($inventaire->ecart_total_valeur)
                        <div class="info-row">
                            <span class="info-label">Écart total (Valeur) :</span>
                            <span class="info-value">
                                <strong class="{{ $inventaire->ecart_total_valeur < 0 ? 'ecart-negatif' : ($inventaire->ecart_total_valeur > 0 ? 'ecart-positif' : 'ecart-neutre') }}">
                                    {{ $inventaire->ecart_total_valeur >= 0 ? '+' : '' }}{{ number_format($inventaire->ecart_total_valeur, 0, ',', ' ') }} FCFA
                                </strong>
                            </span>
                        </div>
                    @endif
                    @if ($inventaire->user)
                        <div class="info-row">
                            <span class="info-label">Réalisé par :</span>
                            <span class="info-value">{{ $inventaire->user->name }}</span>
                        </div>
                    @endif
                    @if ($inventaire->observation)
                        <div class="info-row">
                            <span class="info-label">Observation :</span>
                            <span class="info-value" style="font-style: italic; color: #555;">{{ $inventaire->observation }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tableau des articles -->
        @if ($inventaire->details && count($inventaire->details) > 0)
            <div style="margin-top: 10px;">
                <div class="info-section-title">Détail des Articles</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 35%;">Désignation</th>
                            <th style="width: 15%; text-align: center;">Qté Théorique</th>
                            <th style="width: 15%; text-align: center;">Qté Physique</th>
                            <th style="width: 20%; text-align: center;">Écart</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventaire->details as $index => $detail)
                            @php $ecart = $detail->ecart_quantite; @endphp
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $detail->article->code ?? 'N/A' }}</td>
                                <td>{{ $detail->article->designation ?? 'N/A' }}</td>
                                <td style="text-align: center;">{{ number_format($detail->quantite_theorique, 2, ',', ' ') }}</td>
                                <td style="text-align: center;">{{ number_format($detail->quantite_physique, 2, ',', ' ') }}</td>
                                <td style="text-align: center;">
                                    <span class="{{ $ecart < 0 ? 'ecart-negatif' : ($ecart > 0 ? 'ecart-positif' : 'ecart-neutre') }}">
                                        {{ $ecart >= 0 ? '+' : '' }}{{ number_format($ecart, 2, ',', ' ') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: 700; border-top: 2px solid #333; padding: 12px;">
                                TOTAUX :
                            </td>
                            <td style="text-align: center; font-weight: 700; border-top: 2px solid #333; padding: 12px;">
                                {{ number_format($inventaire->details->sum('quantite_theorique'), 2, ',', ' ') }}
                            </td>
                            <td style="text-align: center; font-weight: 700; border-top: 2px solid #333; padding: 12px;">
                                {{ number_format($inventaire->details->sum('quantite_physique'), 2, ',', ' ') }}
                            </td>
                            <td style="text-align: center; font-weight: 700; border-top: 2px solid #333; padding: 12px; color: #0d47a1;">
                                @php $totalEcart = $inventaire->details->sum('ecart_quantite'); @endphp
                                <span class="{{ $totalEcart < 0 ? 'ecart-negatif' : ($totalEcart > 0 ? 'ecart-positif' : 'ecart-neutre') }}">
                                    {{ $totalEcart >= 0 ? '+' : '' }}{{ number_format($totalEcart, 2, ',', ' ') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Résumé en cartes -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="sc-value">{{ $inventaire->details->count() }}</div>
                    <div class="sc-label">Articles inventoriés</div>
                </div>
                <div class="summary-card deficit">
                    <div class="sc-value">{{ $inventaire->details->where('ecart_quantite', '<', 0)->count() }}</div>
                    <div class="sc-label">Articles en déficit</div>
                </div>
                <div class="summary-card surplus">
                    <div class="sc-value">{{ $inventaire->details->where('ecart_quantite', '>', 0)->count() }}</div>
                    <div class="sc-label">Articles en surplus</div>
                </div>
            </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section" style="margin-top: 50px;">
            <div class="signature-box">
                <strong>Responsable Inventaire</strong>
                <div style="margin-top: 40px;">_________________</div>
            </div>
            <div class="signature-box">
                <strong>Responsable Magazin</strong>
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
                Cette fiche d'inventaire a été générée le {{ now()->format('d/m/Y à H:i:s') }} — {{ $entete->titre ?? 'EKDF' }}.
            </p>
            <p style="margin-top: 10px;">
                <strong>Document officiel — à conserver.</strong>
            </p>
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
