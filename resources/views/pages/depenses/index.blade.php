@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Gestion des Dépenses</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dépenses</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer"></i>&nbsp; Imprimer
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if (request()->filled('categorie') || request()->filled('date_debut') || request()->filled('date_fin'))
                            <li>
                                <a class="dropdown-item" target="_blank"
                                    href="{{ route('depenses.printJournal', request()->only(['categorie', 'date_debut', 'date_fin'])) }}">
                                    Liste filtrée
                                </a>
                            </li>
                        @endif
                        <li><a class="dropdown-item" target="_blank" href="{{ route('depenses.printJournal') }}">Toutes les dépenses</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('depenses.printJournal', ['periode' => 'jour']) }}">Dépenses du jour</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('depenses.printJournal', ['periode' => 'semaine']) }}">Dépenses de la semaine</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('depenses.printJournal', ['periode' => 'mois']) }}">Dépenses du mois</a></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('depenses.printJournal', ['periode' => 'annee']) }}">Dépenses de l'année</a></li>
                    </ul>
                </div>
                @if (\App\Support\Acces::peutCreer())
                <a href="{{ route('gestions_depenses.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i>&nbsp; Nouvelle Dépense
                </a>
                @endif
            </div>
        </div>
    </div>

    <section class="section">
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

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liste des Dépenses</h5>
                        <hr>

                        <!-- Filtres -->
                        <form method="GET" action="{{ route('gestions_depenses.index') }}" class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select name="categorie" class="form-control">
                                        <option value="">Toutes les catégories</option>
                                        @foreach ($categories as $categorie)
                                            <option value="{{ $categorie }}"
                                                {{ request('categorie') == $categorie ? 'selected' : '' }}>
                                                {{ $categorie }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_debut" class="form-control"
                                        value="{{ request('date_debut') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_fin" class="form-control"
                                        value="{{ request('date_fin') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Libellé</th>
                                        <th>Catégorie</th>
                                        <th class="text-end">Montant</th>
                                        <th>Mode de Paiement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($depenses as $index => $depense)
                                        <tr>
                                            <td>{{ $depenses->firstItem() + $index }}</td>
                                            <td>{{ $depense->date_depense->format('d/m/Y') }}</td>
                                            <td>{{ $depense->libelle }}</td>
                                            <td><span class="badge bg-secondary">{{ $depense->categorie }}</span></td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($depense->montant, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td>{{ $depense->mode_paiement ?? '-' }}</td>
                                            <td>
                                                @if (\App\Support\Acces::peutModifier('depense'))
                                                <a href="{{ route('gestions_depenses.edit', $depense->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('gestions_depenses.destroy', $depense->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                Aucune dépense enregistrée
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $depenses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
