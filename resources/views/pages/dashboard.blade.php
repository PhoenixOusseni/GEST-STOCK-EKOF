@extends('layouts.master')

@section('content')

    {{-- ─────────────────────────────────────────────────────────────
         Modern Dashboard
         ───────────────────────────────────────────────────────────── --}}

    <style>
        .dash-bg {
            background: linear-gradient(135deg, #f8faf8 0%, #eef6ee 100%);
            border-radius: 5px;
            padding: 22px 24px;
            margin-bottom: 28px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        }

        .dash-title {
            font-size: 1.55rem;
            font-weight: 700;
            color: #0a0a0a;
            margin: 0;
            letter-spacing: -0.4px;
        }

        .dash-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 2px;
            margin-bottom: 0;
        }

        .dash-bg .btn-primary {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            border-color: #1565c0;
        }

        .dash-bg .btn-primary:hover,
        .dash-bg .btn-primary:focus {
            background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
            border-color: #0d47a1;
        }

        .kpi-card {
            border: none;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .05), 0 4px 16px rgba(15, 23, 42, .04);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
            position: relative;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(15, 23, 42, .06), 0 12px 24px rgba(15, 23, 42, .08);
        }

        .kpi-card .kpi-body {
            padding: 18px 20px;
        }

        .kpi-card .kpi-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .kpi-card .kpi-value {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0a0a0a;
            line-height: 1.1;
        }

        .kpi-card .kpi-currency {
            font-size: 0.75rem;
            color: #64748b;
            margin-left: 4px;
            font-weight: 500;
        }

        .kpi-card .kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            flex-shrink: 0;
        }

        .kpi-card .kpi-foot {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-card .kpi-foot .pill {
            background: #f1f5f2;
            color: #475569;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: 600;
        }

        .kpi-card .strip {
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        /* ── Brand tone system ──
             green = money in / revenue · dark = money out / cost · gray = neutral counts */
        .icon-tone-green      { background: linear-gradient(135deg, #1e88e5, #0d47a1); }
        .icon-tone-green-soft { background: linear-gradient(135deg, #90caf9, #1e88e5); }
        .icon-tone-dark       { background: linear-gradient(135deg, #e53935, #b71c1c); }
        .icon-tone-dark-soft  { background: linear-gradient(135deg, #ef9a9a, #e53935); }
        .icon-tone-gray       { background: linear-gradient(135deg, #78716c, #57534e); }
        .icon-tone-gray-soft  { background: linear-gradient(135deg, #94a3b8, #64748b); }

        .strip-tone-green      { background: #1565c0; }
        .strip-tone-green-soft { background: #64b5f6; }
        .strip-tone-dark       { background: #c62828; }
        .strip-tone-dark-soft  { background: #333333; }
        .strip-tone-gray       { background: #57534e; }
        .strip-tone-gray-soft  { background: #64748b; }

        .panel-card {
            border: none;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .05), 0 4px 16px rgba(15, 23, 42, .04);
        }

        .panel-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-card .card-header h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #0a0a0a;
            margin: 0;
        }

        .panel-card .card-header h5 i {
            color: #1565c0;
            margin-right: 8px;
        }

        .panel-card .card-body {
            padding: 18px 20px;
        }

        .dashboard .btn-outline-primary {
            color: #1565c0;
            border-color: #1565c0;
        }

        .dashboard .btn-outline-primary:hover,
        .dashboard .btn-outline-primary:focus {
            background: #1565c0;
            border-color: #1565c0;
            color: #fff;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #f1f5f9;
            padding: 10px 8px;
        }

        .mini-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.85rem;
            color: #1e293b;
        }

        .mini-table tr:last-child td {
            border-bottom: none;
        }

        .pill-soft {
            background: #e3f2fd;
            color: #1565c0;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-block;
        }

        .pill-soft-success {
            background: #ecfdf5;
            color: #047857;
        }

        .pill-soft-warn {
            background: #f1f1ef;
            color: #262626;
        }

        .account-tile {
            border-radius: 5px;
            padding: 12px 14px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f7f7f6 0%, #ffffff 100%);
            border: 1px solid #e5e5e2;
        }

        .account-tile.caisse {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            border-color: #bbf7d0;
        }

        .account-tile .acc-title {
            font-weight: 700;
            color: #1a1a1a;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .account-tile.caisse .acc-title {
            color: #166534;
        }

        .account-tile .acc-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            margin-top: 3px;
        }

        .stock-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        .stock-item .name {
            font-size: 0.83rem;
            font-weight: 600;
            color: #0a0a0a;
        }

        .stock-item .depot {
            font-size: 0.72rem;
            color: #64748b;
        }

        .stock-item .qty {
            background: #dc2626;
            color: #fff;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .summary-tile {
            background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 14px 12px;
            text-align: center;
        }

        .summary-tile .v {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0d47a1;
            line-height: 1;
        }

        .summary-tile .l {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 600;
        }

        @keyframes montant-clignote {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        .mini-table td.montant-alerte {
            color: #c62828;
            font-weight: 800;
            animation: montant-clignote 1.05s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .mini-table td.montant-alerte { animation: none; }
        }

        @media (max-width: 576px) {
            .summary-grid { grid-template-columns: 1fr; }
            .dash-title { font-size: 1.25rem; }
        }
    </style>

    {{-- Hero header --}}
    <div class="pagetitle">
        <div class="dash-bg d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="dash-title"><i class="bi bi-speedometer2 me-2" style="color:#1565c0;"></i>Tableau de bord</p>
                <p class="dash-subtitle">{{ $depot->designation }}@if($depot->localisation) — {{ $depot->localisation }}@endif · {{ now()->translatedFormat('l d F Y') }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('depots.quitter') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-building me-1"></i> Changer de magazin
                </a>
                <span class="badge bg-light text-dark border px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i> Mis à jour {{ now()->format('H:i') }}
                </span>
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualiser
                </a>
            </div>
        </div>
    </div>

    <section class="section dashboard">

        <div class="row g-3 mb-4">
            @foreach (['jour' => 'Ventes du jour', 'semaine' => 'Ventes de la semaine', 'mois' => 'Ventes du mois', 'annee' => 'Ventes de l\'année'] as $cle => $libelle)
                <div class="col-xl-3 col-md-6">
                    <div class="card kpi-card h-100">
                        <span class="strip strip-tone-green"></span>
                        <div class="kpi-body">
                            <div class="kpi-label">{{ $libelle }}</div>
                            <div class="kpi-value">
                                {{ number_format($ventes_periodes[$cle], 0, ',', ' ') }}<span class="kpi-currency">FCFA</span>
                            </div>
                            <div class="kpi-foot">
                                <span class="pill">{{ $ventes_periodes[$cle . '_count'] }}</span> vente(s) · {{ $depot->designation }}
                                <a href="{{ route('ventes.printPeriode', $cle) }}" target="_blank" class="ms-auto text-decoration-none" style="color:#1565c0;font-weight:700;">
                                    <i class="bi bi-printer"></i> Imprimer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Row 1 : KPI Cards ── --}}
        <div class="row g-3 mb-4">

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-green"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-green"><i class="bi bi-cart-check-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Ventes du mois</div>
                            <div class="kpi-value">
                                {{ number_format($ventes_mois, 0, ',', ' ') }}<span class="kpi-currency">FCFA</span>
                            </div>
                            <div class="kpi-foot">
                                <span class="pill">{{ $total_stats['ventes'] }}</span> transactions
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-dark"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-dark"><i class="bi bi-bag-check-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Achats du mois</div>
                            <div class="kpi-value">
                                {{ number_format($achats_mois, 0, ',', ' ') }}<span class="kpi-currency">FCFA</span>
                            </div>
                            <div class="kpi-foot">
                                <span class="pill">{{ $total_stats['achats'] }}</span> achats
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-green-soft"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-green-soft"><i class="bi bi-cash-coin"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Reste à solder des ventes</div>
                            <div class="kpi-value">
                                {{ number_format($reste_a_solder_ventes, 0, ',', ' ') }}<span class="kpi-currency">FCFA</span>
                            </div>
                            <div class="kpi-foot">
                                <span class="pill">{{ $count_ventes_non_soldees }}</span> ventes non soldées
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-dark-soft"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-dark-soft"><i class="bi bi-arrow-down-circle-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Reste à solder des achats</div>
                            <div class="kpi-value">
                                {{ number_format($reste_a_solder_achats, 0, ',', ' ') }}<span class="kpi-currency">FCFA</span>
                            </div>
                            <div class="kpi-foot">
                                <span class="pill">{{ $count_achats_non_soldes }}</span> achats non soldés
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-dark"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-dark"><i class="bi bi-wallet2"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Dépenses du mois</div>
                            <div class="kpi-value">
                                {{ number_format($depenses_mois, 0, ',', ' ') }}<span class="kpi-currency">FCFA</span>
                            </div>
                            <div class="kpi-foot">
                                <span class="pill">{{ $total_stats['depenses'] }}</span> dépenses
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-gray"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-gray"><i class="bi bi-people-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Clients</div>
                            <div class="kpi-value">{{ number_format($total_stats['clients'], 0, ',', ' ') }}</div>
                            <div class="kpi-foot">clients enregistrés</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-gray-soft"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-gray-soft"><i class="bi bi-truck"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Fournisseurs</div>
                            <div class="kpi-value">{{ number_format($total_stats['fournisseurs'], 0, ',', ' ') }}</div>
                            <div class="kpi-foot">partenaires actifs</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-gray"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-gray"><i class="bi bi-box-seam-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Articles</div>
                            <div class="kpi-value">{{ number_format($total_stats['articles'], 0, ',', ' ') }}</div>
                            <div class="kpi-foot">articles référencés</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card h-100">
                    <span class="strip strip-tone-gray-soft"></span>
                    <div class="kpi-body d-flex align-items-start gap-3">
                        <div class="kpi-icon icon-tone-gray-soft"><i class="bi bi-person-badge-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="kpi-label">Utilisateurs</div>
                            <div class="kpi-value">{{ number_format($total_stats['utilisateurs'], 0, ',', ' ') }}</div>
                            <div class="kpi-foot">comptes</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="card panel-card">
            <div class="card-header">
                <h5><i class="bi bi-exclamation-circle" style="color:#c62828;"></i>Ventes non soldées</h5>
                <a href="{{ route('ventes.non-soldees') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th class="ps-3">N° vente</th>
                            <th>Date</th>
                            <th>Ancienneté</th>
                            <th>Client</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Payé</th>
                            <th class="text-end">Reste à payer</th>
                            <th class="text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventes_non_soldees as $vente)
                            @php
                                $jours = (int) $vente->date_vente->copy()->startOfDay()->diffInDays(now()->copy()->startOfDay(), true);
                                $anciennete = $jours === 0 ? "Aujourd'hui" : ($jours === 1 ? '1 jour' : $jours . ' jours');
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="fw-semibold text-decoration-none">
                                        {{ $vente->numero_vente }}
                                    </a>
                                </td>
                                <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                                <td>{{ $anciennete }}</td>
                                <td>
                                    @if ($vente->client)
                                        {{ $vente->client->raison_sociale ?: $vente->client->nom }}
                                    @else
                                        <span class="text-muted">Client au comptant</span>
                                    @endif
                                </td>
                                <td class="text-end montant-alerte">{{ number_format($vente->montant_total, 0, ',', ' ') }} <small>FCFA</small></td>
                                <td class="text-end montant-alerte">{{ number_format($vente->montant_paye, 0, ',', ' ') }} <small>FCFA</small></td>
                                <td class="text-end montant-alerte">{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} <small>FCFA</small></td>
                                <td class="text-center pe-3">
                                    <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle d-block mb-1" style="font-size:1.5rem;color:#1e88e5;"></i>
                                    Aucune vente non soldée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </section>

@endsection