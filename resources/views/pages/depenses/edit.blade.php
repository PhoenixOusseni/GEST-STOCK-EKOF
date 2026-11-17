@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Modifier la Dépense</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_depenses.index') }}">Dépenses</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_depenses.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> &nbsp; Retour
                </a>
                <form action="{{ route('gestions_depenses.destroy', $depense->id) }}" method="POST"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> &nbsp; Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Modification de la dépense</h5>
                        <hr>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading">Erreurs de validation</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('gestions_depenses.update', $depense->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="small">Date *</label>
                                    <input type="date" class="form-control @error('date_depense') is-invalid @enderror"
                                        name="date_depense"
                                        value="{{ old('date_depense', $depense->date_depense->format('Y-m-d')) }}"
                                        required>
                                    @error('date_depense')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="small">Catégorie *</label>
                                    <select class="form-select @error('categorie') is-invalid @enderror" name="categorie"
                                        required>
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach ($categories as $categorie)
                                            <option value="{{ $categorie }}"
                                                {{ old('categorie', $depense->categorie) == $categorie ? 'selected' : '' }}>
                                                {{ $categorie }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categorie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="small">Montant *</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('montant') is-invalid @enderror" name="montant"
                                        value="{{ old('montant', $depense->montant) }}" required>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small">Libellé *</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                    name="libelle" value="{{ old('libelle', $depense->libelle) }}" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small">Mode de Paiement</label>
                                    <select class="form-select @error('mode_paiement') is-invalid @enderror"
                                        name="mode_paiement">
                                        <option value="">Sélectionner le mode</option>
                                        @php $modePaiement = old('mode_paiement', $depense->mode_paiement); @endphp
                                        <option value="Espèces" {{ $modePaiement == 'Espèces' ? 'selected' : '' }}>
                                            Espèces</option>
                                        <option value="Chèque" {{ $modePaiement == 'Chèque' ? 'selected' : '' }}>
                                            Chèque</option>
                                        <option value="Virement" {{ $modePaiement == 'Virement' ? 'selected' : '' }}>
                                            Virement</option>
                                        <option value="Carte" {{ $modePaiement == 'Carte' ? 'selected' : '' }}>
                                            Carte</option>
                                        <option value="Mobile Money"
                                            {{ $modePaiement == 'Mobile Money' ? 'selected' : '' }}>Mobile Money
                                        </option>
                                    </select>
                                    @error('mode_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="small">Référence</label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror"
                                        name="reference" value="{{ old('reference', $depense->reference) }}">
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small">Observation</label>
                                <textarea class="form-control @error('observation') is-invalid @enderror" name="observation"
                                    rows="3">{{ old('observation', $depense->observation) }}</textarea>
                                @error('observation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-pencil-square"></i>&nbsp; Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
