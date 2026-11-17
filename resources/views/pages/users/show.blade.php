@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Détails Utilisateur: {{ $user->nom }} {{ $user->prenom }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_utilisateurs.index') }}">Utilisateurs</a></li>
                        <li class="breadcrumb-item active">{{ $user->nom }} {{ $user->prenom }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_utilisateurs.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <a href="{{ route('gestions_utilisateurs.edit', $user->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <form action="{{ route('gestions_utilisateurs.destroy', $user->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <!-- Informations Générales -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informations Générales</h5>
                        <hr>
                        <div class="row mb-3">
                            <label class="col-md-4 fw-bold">Nom Complet:</label>
                            <div class="col-md-8">
                                {{ $user->nom }} {{ $user->prenom }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-4 fw-bold">Téléphone:</label>
                            <div class="col-md-8">
                                {{ $user->telephone }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-4 fw-bold">Rôle:</label>
                            <div class="col-md-8">
                                @php
                                    $roleBadges = [
                                        'administrateur' => ['bg-danger', 'bi-shield-lock'],
                                        'caisse' => ['bg-primary', 'bi-cash-coin'],
                                        'magazin' => ['bg-info text-dark', 'bi-building'],
                                    ];
                                    [$roleBg, $roleIcon] = $roleBadges[$user->role] ?? ['bg-secondary', 'bi-person'];
                                @endphp
                                <span class="badge {{ $roleBg }}">
                                    <i class="bi {{ $roleIcon }}"></i> {{ \App\Support\Acces::libelle($user->role) }}
                                </span>
                                @if ($user->depot)
                                    <span class="badge bg-light text-dark border">{{ $user->depot->designation }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de Dates -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informations de Compte</h5>
                        <hr>
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Date de Création:</label>
                            <p class="mb-0">
                                {{ $user->created_at }}
                            </p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Dernière Modification:</label>
                            <p class="mb-0">
                                {{ $user->updated_at }}
                            </p>
                        </div>

                        <hr>

                        <div>
                            <label class="fw-bold small text-muted">ID Utilisateur:</label>
                            <p class="mb-0">
                                <code>{{ $user->id }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions Rapides -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Actions Rapides</h5>
                        <hr>
                        <a href="{{ route('gestions_utilisateurs.edit', $user->id) }}" class="btn btn-warning btn-sm w-100 mb-2">
                            <i class="bi bi-pencil"></i> Modifier les informations
                        </a>

                        <form action="{{ route('gestions_utilisateurs.destroy', $user->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="bi bi-trash"></i> Supprimer le compte
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
