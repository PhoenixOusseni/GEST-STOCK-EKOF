@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Gestion des Utilisateurs</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Utilisateurs</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_utilisateurs.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Ajouter Utilisateur
            </a>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liste des Utilisateurs</h5>
                        <hr>
                        @if ($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Téléphone</th>
                                            <th>Rôle</th>
                                            <th>Magazin</th>
                                            <th>Date Création</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $users->firstItem() + $index }}</td>
                                                <td>
                                                    <strong>{{ $user->nom }} {{ $user->prenom }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $user->telephone }}</span>
                                                </td>
                                                <td>
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
                                                </td>
                                                <td>{{ $user->depot->designation ?? '—' }}</td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $user->created_at ?? 'N/A' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2 justify-content-end">
                                                        <a href="{{ route('gestions_utilisateurs.show', $user->id) }}"
                                                            class="btn btn-success btn-sm" title="Voir">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('gestions_utilisateurs.edit', $user->id) }}"
                                                            class="btn btn-warning btn-sm" title="Modifier">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('gestions_utilisateurs.destroy', $user->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> Aucun utilisateur enregistré.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
