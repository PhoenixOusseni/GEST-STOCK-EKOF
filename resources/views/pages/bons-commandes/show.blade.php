@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Bon {{ $bon->numero_bon }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_bons_commandes.index') }}">Bons de commande</a></li>
                        <li class="breadcrumb-item active">{{ $bon->numero_bon }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_bons_commandes.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <a href="{{ route('bons_commandes.print', $bon->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer"></i> Imprimer
                </a>
                @if ($bon->statut === 'en_cours')
                    <a href="{{ route('gestions_bons_commandes.edit', $bon->id) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i> Modifier
                    </a>
                    <form action="{{ route('bons_commandes.transformer', $bon->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Transformer ce bon en facture ? Le stock du magazin sera diminué et le bon passera à validé.');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-receipt"></i> Transformer en facture
                        </button>
                    </form>
                    <form action="{{ route('gestions_bons_commandes.destroy', $bon->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Supprimer ce bon de commande ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <section class="section">
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

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    Informations
                    @if ($bon->statut === 'valide')
                        <span class="badge bg-success">Validé</span>
                    @else
                        <span class="badge bg-warning text-dark">En cours</span>
                    @endif
                </h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 40%;">Numéro</td>
                                <td>{{ $bon->numero_bon }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Date</td>
                                <td>{{ $bon->date_commande->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Client</td>
                                <td>
                                    @if ($bon->client)
                                        {{ $bon->client->raison_sociale ?: $bon->client->nom }}
                                        @if ($bon->client->telephone)
                                            <br><small class="text-muted">{{ $bon->client->telephone }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Sans client</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Type</td>
                                <td>{{ $bon->type_vente === 'gros' ? 'Gros' : 'Détail' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Montant</td>
                                <td class="fw-bold">{{ number_format($bon->montant_total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Versement prévu</td>
                                <td>{{ number_format($bon->montant_paye, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Reste</td>
                                <td>{{ number_format($bon->reste_a_payer, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Facture</td>
                                <td>
                                    @if ($bon->vente)
                                        <a href="{{ route('gestions_ventes.show', $bon->vente_id) }}">{{ $bon->vente->numero_vente }}</a>
                                    @else
                                        <span class="text-muted">Pas encore transformé</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Articles</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Désignation</th>
                                <th>Magazin</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bon->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->article->code ?? '—' }}</td>
                                    <td>{{ $detail->article->designation ?? '—' }}</td>
                                    <td>{{ $detail->depot->designation ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($detail->quantite, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($detail->prix_vente, 0, ',', ' ') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($detail->prix_total, 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
