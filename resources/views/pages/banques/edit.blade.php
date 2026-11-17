@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Modifier la Banque</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_banques.index') }}">Banques</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_banques.show', $banque->id) }}">{{ $banque->code }}</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_banques.show', $banque->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> &nbsp; Retour
                </a>
                <form action="{{ route('gestions_banques.destroy', $banque->id) }}" method="POST"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette banque ?');">
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
                        <h5 class="card-title">
                            <i class="bi bi-bank"></i> Modification de la Banque
                            <span class="badge bg-primary ms-2">{{ $banque->code }}</span>
                        </h5>
                        <hr>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('gestions_banques.update', $banque->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Informations Principales -->
                            <div class="form-section mb-4">
                                <div class="form-section-title">
                                    <i class="bi bi-info-circle"></i> Informations Principales
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="code" class="small fw-bold">Code Banque</label>
                                        <input type="text" class="form-control" id="code"
                                            value="{{ $banque->code }}" disabled>
                                        <small class="text-muted">Le code n'est pas modifiable</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_compte" class="small fw-bold">
                                            Numéro de Compte <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('numero_compte') is-invalid @enderror"
                                            id="numero_compte" name="numero_compte"
                                            value="{{ old('numero_compte', $banque->numero_compte) }}" required>
                                        @error('numero_compte')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="designation" class="small fw-bold">
                                            Dénomination <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                            id="designation" name="designation"
                                            value="{{ old('designation', $banque->designation) }}" required>
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Coordonnées -->
                            <div class="form-section mb-4">
                                <div class="form-section-title">
                                    <i class="bi bi-geo-alt"></i> Coordonnées
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telephone" class="small fw-bold">
                                            <i class="bi bi-telephone"></i> Téléphone
                                        </label>
                                        <input type="text" class="form-control @error('telephone') is-invalid @enderror"
                                            id="telephone" name="telephone"
                                            value="{{ old('telephone', $banque->telephone) }}">
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="small fw-bold">
                                            <i class="bi bi-envelope"></i> Email
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email"
                                            value="{{ old('email', $banque->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="adresse" class="small fw-bold">
                                            <i class="bi bi-pin-map"></i> Adresse
                                        </label>
                                        <input type="text" class="form-control @error('adresse') is-invalid @enderror"
                                            id="adresse" name="adresse"
                                            value="{{ old('adresse', $banque->adresse) }}">
                                        @error('adresse')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Solde -->
                            <div class="form-section mb-4">
                                <div class="form-section-title">
                                    <i class="bi bi-cash-stack"></i> Solde
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="solde" class="small fw-bold">
                                            Solde Actuel (FCFA)
                                        </label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('solde') is-invalid @enderror"
                                            id="solde" name="solde"
                                            value="{{ old('solde', $banque->solde) }}">
                                        @error('solde')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> &nbsp; Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-cash-stack"></i> Solde Actuel
                        </h5>
                        <div class="text-center py-3">
                            <h3 class="text-{{ $banque->solde >= 0 ? 'success' : 'danger' }} mb-1">
                                {{ number_format($banque->solde, 0, ',', ' ') }} FCFA
                            </h3>
                            <p class="text-muted small mb-0">
                                @if ($banque->solde >= 0)
                                    <i class="bi bi-check-circle-fill text-success"></i> Solde positif
                                @else
                                    <i class="bi bi-exclamation-triangle-fill text-danger"></i> Solde négatif
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> Informations
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted small">Code</span>
                                <span class="badge bg-primary">{{ $banque->code }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted small">ID</span>
                                <span class="badge bg-secondary">#{{ $banque->id }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted small">Créé le</span>
                                <span class="small">{{ $banque->created_at ? $banque->created_at->format('d/m/Y') : 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted small">Modifié le</span>
                                <span class="small">{{ $banque->updated_at ? $banque->updated_at->format('d/m/Y') : 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
