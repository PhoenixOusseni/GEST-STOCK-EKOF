@extends('layouts.master')
@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Gestion des Ventes</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Ventes</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer"></i>&nbsp; Imprimer
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" target="_blank" href="{{ route('ventes.printJournal') }}">Toutes les factures</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('ventes.printPeriode', 'jour') }}">Ventes du jour</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('ventes.printPeriode', 'semaine') }}">Ventes de la semaine</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('ventes.printPeriode', 'mois') }}">Ventes du mois</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('ventes.printPeriode', 'annee') }}">Ventes de l'année</a></li>
                    </ul>
                </div>
                <a href="{{ route('gestions_ventes.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i>&nbsp; Nouvelle Vente
                </a>
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
                        <form action="{{ route('gestions_ventes.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="numero_vente" class="small">N° Vente</label>
                                    <input type="text" class="form-control" id="numero_vente" name="numero_vente"
                                        value="{{ request('numero_vente') }}" placeholder="Rechercher...">
                                </div>
                                <div class="col-md-3">
                                    <label for="client_id" class="small">Client</label>
                                    <select class="form-select" id="client_id" name="client_id">
                                        <option value="">Tous les clients</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->raison_sociale ?? $client->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="type_vente" class="small">Type de Vente</label>
                                    <select class="form-select" id="type_vente" name="type_vente">
                                        <option value="">Tous</option>
                                        <option value="detail" {{ request('type_vente') == 'detail' ? 'selected' : '' }}>Détail</option>
                                        <option value="gros" {{ request('type_vente') == 'gros' ? 'selected' : '' }}>Gros</option>
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
                                <div class="col-md-1" style="margin-top: 27px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search" class="me-2"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste des ventes -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liste des Ventes</h5>
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

                        @if ($ventes->isEmpty())
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle"></i> Aucune vente enregistrée pour le moment.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>N° vente</th>
                                            <th>Client</th>
                                            <th>Type</th>
                                            <th class="text-end">Montant Total</th>
                                            <th class="text-end">Payé</th>
                                            <th class="text-end">Reste</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ventes as $vente)
                                            <tr>
                                                <td><strong>{{ $loop->iteration + ($ventes->currentPage() - 1) * $ventes->perPage() }}</strong></td>
                                                <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                                                <td>{{ $vente->numero_vente }}</td>
                                                <td>
                                                    @if($vente->client)
                                                        {{ $vente->client->raison_sociale ?? $vente->client->nom }}
                                                    @else
                                                        <span class="text-muted fst-italic">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($vente->type_vente === 'gros')
                                                        <span class="badge bg-info text-dark">Gros</span>
                                                    @else
                                                        <span class="badge bg-secondary">Détail</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($vente->montant_total, 0, ',', ' ') }}</td>
                                                <td class="text-end text-success">{{ number_format($vente->montant_paye, 0, ',', ' ') }}</td>
                                                <td class="text-end {{ $vente->reste_a_payer > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    {{ number_format($vente->reste_a_payer, 0, ',', ' ') }}
                                                </td>
                                                <td>
                                                    @if($vente->statut === 'solde')
                                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Soldé</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Non soldé</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('gestions_ventes.show', $vente) }}" class="btn btn-sm btn-success" title="Voir">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if($vente->statut === 'non_solde')
                                                            <a href="{{ route('gestions_ventes.edit', $vente) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-primary" title="Paiement"
                                                                data-bs-toggle="modal" data-bs-target="#modalSolderVente{{ $vente->id }}">
                                                                <i class="bi bi-cash-coin"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($vente->statut === 'non_solde')
                                                @include('partials.modal-solder-vente', ['vente' => $vente])
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $ventes->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <!-- Stat : Total ventes -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-primary mb-1"><i class="bi bi-receipt fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Total Ventes</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['total_ventes'] }}</h4>
                    </div>
                </div>

                <!-- Stat : Chiffre d'affaires total -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-success mb-1"><i class="bi bi-cash-stack fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">CA Total</h6>
                        <h6 class="fw-bold mb-0">{{ number_format($stats['montant_total'], 0, ',', ' ') }}</h6>
                    </div>
                </div>

                <!-- Stat : Ventes du mois -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-warning mb-1"><i class="bi bi-calendar-month fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Ventes ce mois</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['ventes_mois'] }}</h4>
                    </div>
                </div>

                <!-- Stat : CA du mois -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-info mb-1"><i class="bi bi-graph-up-arrow fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">CA ce mois</h6>
                        <h6 class="fw-bold mb-0">{{ number_format($stats['montant_mois'], 0, ',', ' ') }}</h6>
                    </div>
                </div>

                <!-- Stat : Clients actifs -->
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-1"><i class="bi bi-people fs-3"></i></div>
                        <h6 class="card-title text-muted small mb-1">Clients actifs</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['nb_clients_actifs'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
