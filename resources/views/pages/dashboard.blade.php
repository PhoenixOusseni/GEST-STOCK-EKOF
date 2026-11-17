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

        .chart-wrap {
            min-height: 320px;
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

        {{-- ── Row 2 : Charts ── --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-8">
                <div class="card panel-card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up-arrow"></i>Ventes vs Achats — 12 derniers mois</h5>
                        <span class="pill-soft"><i class="bi bi-calendar3 me-1"></i>Mensuel</span>
                    </div>
                    <div class="card-body">
                        <div id="chartVentesAchats" class="chart-wrap"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card panel-card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-pie-chart-fill"></i>Flux de trésorerie</h5>
                        <span class="pill-soft pill-soft-success">Ce mois</span>
                    </div>
                    <div class="card-body">
                        <div id="chartTresorerie" class="chart-wrap"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Row 3 : Encaissements/Décaissements + Top Articles ── --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-7">
                <div class="card panel-card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-bar-chart-line-fill"></i>Encaissements & Décaissements</h5>
                        <span class="pill-soft"><i class="bi bi-calendar3 me-1"></i>12 mois</span>
                    </div>
                    <div class="card-body">
                        <div id="chartFlux" class="chart-wrap"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card panel-card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-trophy-fill"></i>Top 5 articles vendus</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartTopArticles" class="chart-wrap" style="min-height:280px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Row 4 : Récents + Aside ── --}}
        <div class="row g-3">

            {{-- Left column --}}
            <div class="col-xl-8">

                {{-- Ventes récentes --}}
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-receipt"></i>Ventes récentes</h5>
                        <a href="{{ route('gestions_ventes.index') }}" class="btn btn-sm btn-outline-primary">
                            Voir tout <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Client</th>
                                    <th class="text-end">Montant</th>
                                    <th>Date</th>
                                    <th class="text-center pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventes_recentes as $vente)
                                    <tr>
                                        <td class="ps-3"><span class="pill-soft">#{{ $vente->id }}</span></td>
                                        <td>{{ $vente->client->nom ?? 'Non spécifié' }}</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($vente->montant_total, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                        </td>
                                        <td><small class="text-muted">{{ $vente->created_at->format('d/m/Y H:i') }}</small></td>
                                        <td class="text-center pe-3">
                                            <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;color:#cbd5e1;"></i>
                                            Aucune vente récente
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Achats récents --}}
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-bag-check"></i>Achats récents</h5>
                        <a href="{{ route('gestions_achats.index') }}" class="btn btn-sm btn-outline-primary">
                            Voir tout <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Fournisseur</th>
                                    <th class="text-end pe-3">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($achats_recents as $achat)
                                    <tr>
                                        <td class="ps-3"><span class="pill-soft pill-soft-warn">#{{ $achat->id }}</span></td>
                                        <td>{{ $achat->fournisseur->nom ?? 'Non spécifié' }}</td>
                                        <td class="text-end pe-3 fw-bold">
                                            {{ number_format($achat->montant_total, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;color:#cbd5e1;"></i>
                                            Aucun achat récent
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Dépenses récentes --}}
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-wallet2"></i>Dépenses récentes</h5>
                        <a href="{{ route('gestions_depenses.index') }}" class="btn btn-sm btn-outline-primary">
                            Voir tout <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">Libellé</th>
                                    <th>Catégorie</th>
                                    <th class="text-end pe-3">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depenses_recentes as $depense)
                                    <tr>
                                        <td class="ps-3">{{ $depense->libelle }}</td>
                                        <td><span class="pill-soft pill-soft-warn">{{ $depense->categorie }}</span></td>
                                        <td class="text-end pe-3 fw-bold">
                                            {{ number_format($depense->montant, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;color:#cbd5e1;"></i>
                                            Aucune dépense récente
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Top Articles Vendus (table détaillée) --}}
                <div class="card panel-card">
                    <div class="card-header">
                        <h5><i class="bi bi-trophy"></i>Top 5 articles vendus — détail</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">Article</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end pe-3">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_articles_ventes as $article)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $article->designation }}</td>
                                        <td class="text-center">
                                            <span class="pill-soft">{{ $article->total_quantite }}</span>
                                        </td>
                                        <td class="text-end pe-3 fw-bold">
                                            {{ number_format($article->total_montant, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Aucune vente</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Right column --}}
            <div class="col-xl-4">

                {{-- Résumé Aujourd'hui --}}
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-sun"></i>Résumé d'aujourd'hui</h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-grid">
                            <div class="summary-tile">
                                <div class="v">{{ $today_stats['ventes_count'] }}</div>
                                <div class="l">Ventes</div>
                            </div>
                            <div class="summary-tile">
                                <div class="v">{{ $today_stats['achats_count'] }}</div>
                                <div class="l">Achats</div>
                            </div>
                            <div class="summary-tile">
                                <div class="v">{{ number_format($today_stats['encaissements'], 0, ',', ' ') }}</div>
                                <div class="l">Encaissé</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Banques & Caisses --}}
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-bank"></i>Banques & Caisses</h5>
                    </div>
                    <div class="card-body" style="max-height:360px; overflow-y:auto;">
                        @if($banques_soldes->count())
                            <div class="mb-2">
                                <small class="text-uppercase text-muted fw-bold" style="font-size:0.7rem; letter-spacing:.06em;">
                                    <i class="bi bi-bank2 me-1"></i>Banques
                                </small>
                            </div>
                            @foreach($banques_soldes as $b)
                                <div class="account-tile">
                                    <div class="acc-title"><i class="bi bi-building me-1"></i>{{ $b['designation'] }}</div>
                                    <div class="acc-row">
                                        <span class="text-muted">Encaissé</span>
                                        <span class="text-success fw-bold">{{ number_format($b['total_encaisse'], 0, ',', ' ') }}</span>
                                    </div>
                                    <div class="acc-row">
                                        <span class="text-muted">Décaissé</span>
                                        <span class="text-danger fw-bold">{{ number_format($b['total_decaisse'], 0, ',', ' ') }}</span>
                                    </div>
                                    <div class="acc-row">
                                        <span class="text-muted">À recouvrir</span>
                                        <span class="fw-bold" style="color:#57534e;">{{ number_format($b['solde_recouvrir'], 0, ',', ' ') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($caisses_soldes->count())
                            <div class="mb-2 mt-2">
                                <small class="text-uppercase text-muted fw-bold" style="font-size:0.7rem; letter-spacing:.06em;">
                                    <i class="bi bi-cash-stack me-1"></i>Caisses
                                </small>
                            </div>
                            @foreach($caisses_soldes as $c)
                                <div class="account-tile caisse">
                                    <div class="acc-title"><i class="bi bi-safe me-1"></i>{{ $c['designation'] }}</div>
                                    <div class="acc-row">
                                        <span class="text-muted">Encaissé</span>
                                        <span class="text-success fw-bold">{{ number_format($c['total_encaisse'], 0, ',', ' ') }}</span>
                                    </div>
                                    <div class="acc-row">
                                        <span class="text-muted">Décaissé</span>
                                        <span class="text-danger fw-bold">{{ number_format($c['total_decaisse'], 0, ',', ' ') }}</span>
                                    </div>
                                    <div class="acc-row">
                                        <span class="text-muted">À recouvrir</span>
                                        <span class="fw-bold" style="color:#166534;">{{ number_format($c['solde_recouvrir'], 0, ',', ' ') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($banques_soldes->isEmpty() && $caisses_soldes->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;color:#cbd5e1;"></i>
                                Aucune banque ni caisse
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Stock Faible --}}
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;"></i>Stock faible</h5>
                        @if ($stock_faible->count() > 0)
                            <span class="pill-soft" style="background:#fee2e2; color:#b91c1c;">{{ $stock_faible->count() }}</span>
                        @endif
                    </div>
                    <div class="card-body" style="max-height:300px; overflow-y:auto;">
                        @if ($stock_faible->count() > 0)
                            @foreach ($stock_faible as $article)
                                <div class="stock-item">
                                    <div>
                                        <div class="name">{{ $article->article->designation ?? 'N/A' }}</div>
                                        <div class="depot">{{ $article->depot->designation ?? 'N/A' }}</div>
                                    </div>
                                    <span class="qty">{{ $article->quantite_disponible }} / {{ $article->quantite_minimale }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle-fill d-block mb-2" style="font-size:2rem; color:#1e88e5;"></i>
                                <small class="text-muted">Tous les stocks sont normaux</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Top Articles Achetés --}}
                <div class="card panel-card">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i>Top 5 articles achetés</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">Article</th>
                                    <th class="text-center pe-3">Qté</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_articles_achats as $article)
                                    <tr>
                                        <td class="ps-3">{{ $article->designation }}</td>
                                        <td class="text-center pe-3">
                                            <span class="pill-soft pill-soft-warn">{{ $article->total_quantite }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Aucun achat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        (function () {
            const labels        = @json($chart_data['labels']);
            const ventes        = @json($chart_data['ventes']);
            const achats        = @json($chart_data['achats']);
            const encaissements = @json($chart_data['encaissements']);
            const decaissements = @json($chart_data['decaissements']);

            const topArticlesLabels = @json($top_articles_ventes->pluck('designation'));
            const topArticlesValues = @json($top_articles_ventes->pluck('total_quantite'));

            const fmt = (v) => new Intl.NumberFormat('fr-FR').format(Math.round(v)) + ' FCFA';
            const compact = (v) => new Intl.NumberFormat('fr-FR', { notation: 'compact', maximumFractionDigits: 1 }).format(v);

            // 1) Ventes vs Achats (area)
            if (document.querySelector('#chartVentesAchats')) {
                new ApexCharts(document.querySelector('#chartVentesAchats'), {
                    chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [
                        { name: 'Ventes', data: ventes },
                        { name: 'Achats', data: achats }
                    ],
                    colors: ['#1565c0', '#c62828'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
                    },
                    xaxis: { categories: labels, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                    yaxis: { labels: { formatter: compact, style: { colors: '#64748b', fontSize: '11px' } } },
                    tooltip: { y: { formatter: fmt } },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                    legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' }
                }).render();
            }

            // 2) Trésorerie du mois (donut)
            if (document.querySelector('#chartTresorerie')) {
                new ApexCharts(document.querySelector('#chartTresorerie'), {
                    chart: { type: 'donut', height: 320, fontFamily: 'inherit' },
                    series: [
                        {{ (float) $ventes_mois }},
                        {{ (float) $achats_mois }},
                        {{ (float) $encaissements_mois }},
                        {{ (float) $decaissements_mois }}
                    ],
                    labels: ['Ventes', 'Achats', 'Encaissements', 'Décaissements'],
                    colors: ['#1565c0', '#c62828', '#90caf9', '#ef9a9a'],
                    legend: { position: 'bottom', fontSize: '12px' },
                    dataLabels: { formatter: (val) => Math.round(val) + '%' },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '68%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: (w) => fmt(w.globals.seriesTotals.reduce((a, b) => a + b, 0))
                                    }
                                }
                            }
                        }
                    },
                    tooltip: { y: { formatter: fmt } }
                }).render();
            }

            // 3) Flux mensuels (barres groupées)
            if (document.querySelector('#chartFlux')) {
                new ApexCharts(document.querySelector('#chartFlux'), {
                    chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [
                        { name: 'Encaissements', data: encaissements },
                        { name: 'Décaissements', data: decaissements }
                    ],
                    colors: ['#1565c0', '#e53935'],
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 6, borderRadiusApplication: 'end' } },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 },
                    xaxis: { categories: labels, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                    yaxis: { labels: { formatter: compact, style: { colors: '#64748b', fontSize: '11px' } } },
                    tooltip: { y: { formatter: fmt } },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                    legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' }
                }).render();
            }

            // 4) Top 5 articles vendus (barres horizontales)
            if (document.querySelector('#chartTopArticles') && topArticlesValues.length) {
                new ApexCharts(document.querySelector('#chartTopArticles'), {
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [{ name: 'Quantité vendue', data: topArticlesValues.map(Number) }],
                    colors: ['#1976d2'],
                    plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '60%', distributed: false } },
                    dataLabels: { enabled: true, style: { fontSize: '11px', colors: ['#fff'] } },
                    xaxis: { categories: topArticlesLabels, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                    yaxis: { labels: { style: { colors: '#0a0a0a', fontSize: '12px', fontWeight: 600 } } },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                    tooltip: { y: { formatter: (v) => v + ' unités' } }
                }).render();
            }
        })();
    </script>
@endsection
