@extends('layouts.master')
@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Ventes Non Soldées</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_ventes.index') }}">Ventes</a></li>
                        <li class="breadcrumb-item active">Non Soldées</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_ventes.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i>&nbsp; Retour aux Ventes
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <!-- Filtres -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filtres</h5>
                        <form action="{{ route('ventes.non-soldees') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="numero_vente" class="small">N° Vente</label>
                                    <input type="text" class="form-control" id="numero_vente" name="numero_vente"
                                        value="{{ request('numero_vente') }}" placeholder="Rechercher...">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_debut" class="small">Date début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut"
                                        value="{{ request('date_debut') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_fin" class="small">Date fin</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin"
                                        value="{{ request('date_fin') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="bi bi-search"></i> Filtrer
                                    </button>
                                    <a href="{{ route('ventes.non-soldees') }}" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-x"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-exclamation-circle text-warning"></i>
                            Ventes avec Solde Restant
                            <span class="badge bg-warning text-dark ms-2">{{ $ventes->total() }}</span>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>N° Vente</th>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th class="text-end">Montant Total</th>
                                        <th class="text-end">Montant Payé</th>
                                        <th class="text-end">Reste à Payer</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ventes as $vente)
                                        <tr>
                                            <td>
                                                <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="fw-semibold text-decoration-none">
                                                    {{ $vente->numero_vente }}
                                                </a>
                                            </td>
                                            <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                                            <td>
                                                @if($vente->client)
                                                    {{ $vente->client->raison_sociale ?? $vente->client->nom }}
                                                @else
                                                    <span class="text-muted fst-italic">Sans client</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end text-success fw-semibold">{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end text-danger fw-bold">{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-center">
                                                <a href="{{ route('gestions_ventes.show', $vente->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if (\App\Support\Acces::peutModifier('facture'))
                                                <a href="{{ route('gestions_ventes.edit', $vente->id) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endif
                                                @if (\App\Support\Acces::peutCreer())
                                                <button type="button" class="btn btn-sm btn-outline-success" title="Paiement"
                                                    data-bs-toggle="modal" data-bs-target="#modalSolderVente{{ $vente->id }}">
                                                    <i class="bi bi-cash-coin"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @include('partials.modal-solder-vente', ['vente' => $vente])
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                                Toutes les ventes sont soldées.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $ventes->links() }}
                    </div>
                </div>
            </div>

            <!-- Sidebar stats -->
            <div class="col-lg-2">
                <div style="position: sticky; top: 80px;">
                    <div class="side-stat is-blue">
                        <span class="side-stat-icon"><i class="bi bi-exclamation-circle"></i></span>
                        <div>
                            <p>Ventes</p>
                            <strong>{{ $ventes->total() }}</strong>
                        </div>
                    </div>
                    <div class="side-stat is-red">
                        <span class="side-stat-icon"><i class="bi bi-cash-stack"></i></span>
                        <div>
                            <p>Total restant</p>
                            <strong>{{ number_format($totalReste, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
