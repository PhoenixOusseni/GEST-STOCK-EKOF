@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Modifier l'Utilisateur: {{ $user->nom }} {{ $user->prenom }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_utilisateurs.index') }}">Utilisateurs</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_utilisateurs.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Formulaire de Modification d'Utilisateur</h5>
                        <hr>
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <strong>Erreurs de validation:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('gestions_utilisateurs.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label for="nom" class="col-md-4 col-form-label">Nom <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                        id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                    @error('nom')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="prenom" class="col-md-4 col-form-label">Prénom <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                        id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                    @error('prenom')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="telephone" class="col-md-4 col-form-label">Téléphone <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror"
                                        id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" required>
                                    @error('telephone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label">Mot de passe (Laisser vide pour conserver)</label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 caractères (si modification)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password_confirmation" class="col-md-4 col-form-label">Confirmer Mot de passe</label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>

                            @include('pages.users._role')

                            <div class="row mb-3">
                                <div class="col-md-8 offset-md-4">
                                    <div class="alert alert-light">
                                        <small class="text-muted">
                                            <strong>Créé le:</strong> {{ $user->created_at }}<br>
                                            <strong>Dernière modification:</strong> {{ $user->updated_at }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mx-0 mb-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
