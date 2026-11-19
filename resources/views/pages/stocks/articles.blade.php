@extends('layouts.master')
@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Articles en stock</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Stock</li>
                        <li class="breadcrumb-item active">Articles</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('stocks.articles.print', $recherche !== '' ? ['q' => $recherche] : []) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer"></i>&nbsp; Imprimer
                </a>
                <span class="badge bg-primary fs-6">{{ $depot->designation }}</span>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="text-muted small">Articles</div>
                        <div class="fs-4 fw-semibold">{{ $articles->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="text-muted small">Stock total</div>
                        <div class="fs-4 fw-semibold">{{ number_format($articles->sum('stock_reel'), 0, ',', ' ') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="text-muted small">Sans stock</div>
                        <div class="fs-4 fw-semibold">{{ $articles->where('stock_reel', '<=', 0)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('stocks.articles') }}" class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label for="q" class="form-label small mb-1">Rechercher un article</label>
                        <input type="search" class="form-control" id="q" name="q" value="{{ $recherche }}" placeholder="Nom ou code">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i> Rechercher
                        </button>
                        @if ($recherche !== '')
                            <a href="{{ route('stocks.articles') }}" class="btn btn-secondary btn-sm">Effacer</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Stock réel — {{ $depot->designation }}</h5>
                @if ($articles->isEmpty())
                    <div class="alert alert-info mb-0">
                        Aucun article ne correspond à cette recherche dans ce magazin.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Article</th>
                                    <th class="text-end">Stock réel</th>
                                    <th class="text-end">Prix de vente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articles as $article)
                                    @php $stock = (float) $article->stock_reel; @endphp
                                    <tr>
                                        <td>{{ $article->code }}</td>
                                        <td>{{ $article->designation }}</td>
                                        <td class="text-end fw-semibold {{ $stock <= 0 ? 'text-danger' : '' }}">
                                            {{ number_format($stock, $stock == floor($stock) ? 0 : 2, ',', ' ') }}
                                        </td>
                                        <td class="text-end">{{ number_format($article->prix_vente, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
