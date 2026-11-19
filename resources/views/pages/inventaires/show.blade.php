@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Détails Inventaire {{ $inventaire->numero_inventaire }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_inventaires.index') }}">Inventaires</a></li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </nav>
                @include('partials.doc-auteur', ['nom' => $inventaire->nomAuteur()])
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_inventaires.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                @if (\App\Support\Acces::estAdmin())
                <form action="{{ route('gestions_inventaires.destroy', $inventaire->id) }}" method="POST"
                    onsubmit="return confirm('Supprimer cet inventaire ?');">
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
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Informations</h5>
                <hr>
                <table class="table">
                    <tr>
                        <th>N° Inventaire:</th>
                        <td>{{ $inventaire->numero_inventaire }}</td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td>{{ $inventaire->date_inventaire->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Magazin:</th>
                        <td>{{ $inventaire->depot->designation ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            @if ($inventaire->statut == 'valide')
                                <span class="badge bg-success">Validé</span>
                            @else
                                <span class="badge bg-warning">En cours</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Écart Total:</th>
                        <td>{{ $inventaire->ecart_total_quantite }}</td>
                    </tr>
                </table>

                <h5 class="card-title">Détails</h5>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Qté Théorique</th>
                            <th>Qté Physique</th>
                            <th>Écart</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventaire->details as $detail)
                            <tr>
                                <td>{{ $detail->article->designation }}</td>
                                <td>{{ $detail->quantite_theorique }}</td>
                                <td>{{ $detail->quantite_physique }}</td>
                                <td>{{ $detail->ecart_quantite }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
