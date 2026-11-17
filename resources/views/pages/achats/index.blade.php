@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Gestion des Achats</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Achats</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('achats.printJournal') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-printer"></i>&nbsp; Imprimer
                </a>
                @if (\App\Support\Acces::peutCreer())
                <a href="{{ route('gestions_achats.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i>&nbsp; Nouvel Achat
                </a>
                @endif
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <!-- Filtres -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filtres de recherche</h5>
                        <form action="{{ route('gestions_achats.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="numero_achat" class="small">N° Achat</label>
                                    <input type="text" class="form-control" id="numero_achat" name="numero_achat"
                                        value="{{ request('numero_achat') }}" placeholder="Rechercher...">
                                </div>
                                <div class="col-md-3">
                                    <label for="fournisseur_id" class="small">Fournisseur</label>
                                    <select class="form-select" id="fournisseur_id" name="fournisseur_id">
                                        <option value="">Tous les fournisseurs</option>
                                        @foreach ($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}"
                                                {{ request('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                {{ $fournisseur->raison_sociale ?? $fournisseur->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_debut" class="small">Date début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut"
                                        value="{{ request('date_debut') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_fin" class="small">Date fin</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin"
                                        value="{{ request('date_fin') }}">
                                </div>
                                <div class="col-md-2" style="margin-top: 27px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search" class="me-2"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste des achats -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liste des Achats</h5>
                        <hr>
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($achats->isEmpty())
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle"></i> Aucun achat enregistré pour le moment.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>N° Achat</th>
                                            <th>Fournisseur</th>
                                            <th>Désignation</th>
                                            <th class="text-end">Montant Total</th>
                                            <th class="text-end">Payé</th>
                                            <th class="text-end">Reste</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($achats as $index => $achat)
                                            <tr>
                                                <td><strong>{{ $loop->iteration + ($achats->currentPage() - 1) * $achats->perPage() }}</strong></td>
                                                <td>{{ $achat->date_achat->format('d/m/Y') }}</td>
                                                <td>{{ $achat->numero_achat }}</td>
                                                <td>
                                                    @if($achat->fournisseur)
                                                        {{ $achat->fournisseur->raison_sociale ?? $achat->fournisseur->nom }}
                                                    @else
                                                        <span class="text-muted fst-italic">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $achat->designation ?? '—' }}</td>
                                                <td class="text-end">{{ number_format($achat->montant_total, 0, ',', ' ') }}</td>
                                                <td class="text-end text-success">{{ number_format($achat->montant_paye, 0, ',', ' ') }}</td>
                                                <td class="text-end {{ $achat->reste_a_payer > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    {{ number_format($achat->reste_a_payer, 0, ',', ' ') }}
                                                </td>
                                                <td>
                                                    @if($achat->statut === 'solde')
                                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Soldé</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Non soldé</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('gestions_achats.show', $achat->id) }}" class="btn btn-sm btn-success" title="Voir">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $achats->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <!-- Stat : Total achats -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-primary mb-1"><i class="bi bi-receipt fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Total Achats</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['total_achats'] }}</h4>
                    </div>
                </div>

                <!-- Stat : Montant total -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-success mb-1"><i class="bi bi-cash-stack fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Montant Total</h6>
                        <h6 class="fw-bold mb-0">{{ number_format($stats['montant_total'], 0, ',', ' ') }}</h6>
                    </div>
                </div>

                <!-- Stat : Achats du mois -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-warning mb-1"><i class="bi bi-calendar-month fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Achats ce mois</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['achats_mois'] }}</h4>
                    </div>
                </div>

                <!-- Stat : Montant du mois -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-info mb-1"><i class="bi bi-graph-up-arrow fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Montant ce mois</h6>
                        <h6 class="fw-bold mb-0">{{ number_format($stats['montant_mois'], 0, ',', ' ') }}</h6>
                    </div>
                </div>

                <!-- Stat : Fournisseurs actifs -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-1"><i class="bi bi-people fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Fournisseurs actifs</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['nb_fournisseurs_actifs'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
