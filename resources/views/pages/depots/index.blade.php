@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1>Magazins</h1>
                <p class="text-muted mb-0">Choisissez un magazin pour consulter ses informations.</p>
            </div>
            @if (\App\Support\Acces::estAdmin())
            <a href="{{ route('gestions_depots.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Nouveau magazin
            </a>
            @endif
        </div>
    </div>

    <section class="section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($depots->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-building text-muted" style="font-size:2rem;"></i>
                    <p class="mt-3 mb-3">Aucun magazin n'est encore enregistré.</p>
                    @if (\App\Support\Acces::estAdmin())
                    <a href="{{ route('gestions_depots.create') }}" class="btn btn-primary">Créer le premier magazin</a>
                    @endif
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach ($depots as $depot)
                    <div class="col-xl-4 col-md-6">
                        <a href="{{ route('gestions_depots.show', $depot->id) }}" class="depot-card">
                            <span class="depot-card-code">{{ $depot->code }}</span>
                            <strong>{{ $depot->designation }}</strong>
                            <span class="depot-card-place">
                                <i class="bi bi-geo-alt"></i>
                                {{ $depot->localisation ?: 'Localisation non renseignée' }}
                            </span>
                            <span class="depot-card-meta">
                                <span>
                                    <small>Articles en stock</small>
                                    <b>{{ $depot->articles_en_stock }}</b>
                                </span>
                                <span>
                                    <small>Quantité</small>
                                    <b>{{ number_format($depot->quantite_totale ?? 0, 0, ',', ' ') }}</b>
                                </span>
                                <span>
                                    <small>Ventes du mois</small>
                                    <b class="is-amount">{{ number_format($ventesMois[$depot->id] ?? 0, 0, ',', ' ') }}</b>
                                </span>
                            </span>
                            @if ($depot->responsable || $depot->contact)
                                <span class="depot-card-who">
                                    <i class="bi bi-person"></i>
                                    {{ $depot->responsable ?: 'Responsable non renseigné' }}
                                    @if ($depot->contact)
                                        · {{ $depot->contact }}
                                    @endif
                                </span>
                            @endif
                            <span class="depot-card-go">Voir les détails <i class="bi bi-arrow-right"></i></span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
