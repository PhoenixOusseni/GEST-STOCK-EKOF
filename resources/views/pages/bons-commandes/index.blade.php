@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Bons de commande</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Bons de commande</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_bons_commandes.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i>&nbsp; Nouveau bon
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filtres</h5>
                        <form action="{{ route('gestions_bons_commandes.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small">N° bon</label>
                                    <input type="text" class="form-control" name="numero_bon" value="{{ request('numero_bon') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Client</label>
                                    <select class="form-select" name="client_id">
                                        <option value="">Tous les clients</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>
                                                {{ $client->raison_sociale ?? $client->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small">Statut</label>
                                    <select class="form-select" name="statut">
                                        <option value="">Tous</option>
                                        <option value="en_cours" @selected(request('statut') === 'en_cours')>En cours</option>
                                        <option value="valide" @selected(request('statut') === 'valide')>Validé</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small">Du</label>
                                    <input type="date" class="form-control" name="date_debut" value="{{ request('date_debut') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="small">Au</label>
                                    <input type="date" class="form-control" name="date_fin" value="{{ request('date_fin') }}">
                                </div>
                                <div class="col-md-2 mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liste des bons</h5>
                        <hr>
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($bons->isEmpty())
                            <div class="alert alert-info">Aucun bon de commande pour ce magazin.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>N° bon</th>
                                            <th>Client</th>
                                            <th>Type</th>
                                            <th class="text-end">Montant</th>
                                            <th>Statut</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bons as $bon)
                                            <tr>
                                                <td>{{ $bon->date_commande->format('d/m/Y') }}</td>
                                                <td>{{ $bon->numero_bon }}</td>
                                                <td>{{ $bon->client ? ($bon->client->raison_sociale ?: $bon->client->nom) : '—' }}</td>
                                                <td>{{ $bon->type_vente === 'gros' ? 'Gros' : 'Détail' }}</td>
                                                <td class="text-end">{{ number_format($bon->montant_total, 0, ',', ' ') }}</td>
                                                <td>
                                                    @if ($bon->statut === 'valide')
                                                        <span class="badge bg-success">Validé</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">En cours</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('gestions_bons_commandes.show', $bon->id) }}" class="btn btn-sm btn-success" title="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if ($bon->statut === 'en_cours')
                                                        <a href="{{ route('gestions_bons_commandes.edit', $bon->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $bons->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted small">Bons</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted small">En cours</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['en_cours'] }}</h4>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted small">Validés</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['valides'] }}</h4>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted small">Montant en cours</h6>
                        <h6 class="fw-bold mb-0">{{ number_format($stats['montant_en_cours'], 0, ',', ' ') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
