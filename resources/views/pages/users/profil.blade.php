@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <h1>Mon Profil</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Carte résumé -->
            <div class="col-xl-4">
                <div class="pf-sticky">
                    <div class="pf-summary-card">
                        <div class="pf-summary-accent"></div>
                        <div class="pf-summary-body">
                            <div class="pf-avatar">
                                {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                            </div>
                            <h2 class="pf-name">{{ $user->prenom }} {{ $user->nom }}</h2>

                            <span class="pf-role-badge pf-role-{{ $user->role }}">
                                <i class="bi bi-shield-check"></i> {{ ucfirst($user->role) }}
                            </span>

                            <div class="pf-info-list">
                                <div class="pf-info-row">
                                    <i class="bi bi-telephone"></i>
                                    <span>{{ $user->telephone }}</span>
                                </div>
                                <div class="pf-info-row">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Membre depuis le {{ $user->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="pf-info-row">
                                    <i class="bi bi-clock-history"></i>
                                    <span>Mis à jour {{ $user->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails / édition -->
            <div class="col-xl-8">
                <div class="card pf-card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-pills pf-nav-pills mb-4" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview"
                                    type="button">
                                    <i class="bi bi-person-lines-fill"></i> Aperçu
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit"
                                    type="button">
                                    <i class="bi bi-pencil-square"></i> Modifier le profil
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password"
                                    type="button">
                                    <i class="bi bi-shield-lock"></i> Mot de passe
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Onglet Aperçu -->
                            <div class="tab-pane fade show active" id="profile-overview">
                                <div class="pf-section">
                                    <div class="pf-section-title"><i class="bi bi-person"></i> Identité</div>
                                    <div class="pf-detail-row">
                                        <span class="pf-detail-label">Prénom</span>
                                        <span class="pf-detail-value">{{ $user->prenom }}</span>
                                    </div>
                                    <div class="pf-detail-row">
                                        <span class="pf-detail-label">Nom</span>
                                        <span class="pf-detail-value">{{ $user->nom }}</span>
                                    </div>
                                    <div class="pf-detail-row">
                                        <span class="pf-detail-label">Téléphone</span>
                                        <span class="pf-detail-value">{{ $user->telephone }}</span>
                                    </div>
                                    <div class="pf-detail-row">
                                        <span class="pf-detail-label">Rôle</span>
                                        <span class="pf-detail-value">
                                            <span class="pf-role-badge pf-role-{{ $user->role }} pf-role-badge-sm">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="pf-section">
                                    <div class="pf-section-title"><i class="bi bi-clock-history"></i> Activité du
                                        compte</div>
                                    <div class="pf-detail-row">
                                        <span class="pf-detail-label">Date de création</span>
                                        <span class="pf-detail-value">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                                    </div>
                                    <div class="pf-detail-row pf-detail-row-last">
                                        <span class="pf-detail-label">Dernière modification</span>
                                        <span
                                            class="pf-detail-value">{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Onglet Modifier le Profil -->
                            <div class="tab-pane fade" id="profile-edit">
                                <form action="{{ route('utilisateurs.updateProfil', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="pf-section">
                                        <div class="pf-section-title"><i class="bi bi-person"></i> Identité</div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="prenom" class="small fw-semibold">Prénom</label>
                                                <input type="text" name="prenom"
                                                    class="form-control @error('prenom') is-invalid @enderror"
                                                    id="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                                @error('prenom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nom" class="small fw-semibold">Nom</label>
                                                <input type="text" name="nom"
                                                    class="form-control @error('nom') is-invalid @enderror" id="nom"
                                                    value="{{ old('nom', $user->nom) }}" required>
                                                @error('nom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="telephone" class="small fw-semibold">Téléphone</label>
                                                <input type="text" name="telephone"
                                                    class="form-control @error('telephone') is-invalid @enderror"
                                                    id="telephone" value="{{ old('telephone', $user->telephone) }}"
                                                    required>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-check-circle"></i> Enregistrer les modifications
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Onglet Changer le Mot de Passe -->
                            <div class="tab-pane fade" id="profile-change-password">
                                <form action="{{ route('utilisateurs.updatePassword', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Champs cachés pour préserver les données actuelles -->
                                    <input type="hidden" name="nom" value="{{ $user->nom }}">
                                    <input type="hidden" name="prenom" value="{{ $user->prenom }}">

                                    <div class="pf-section">
                                        <div class="pf-section-title"><i class="bi bi-shield-lock"></i> Sécurité</div>

                                        <div class="mb-3">
                                            <label for="current_password" class="small fw-semibold">Mot de passe
                                                actuel</label>
                                            <input type="password" name="current_password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                id="current_password">
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="password" class="small fw-semibold">Nouveau mot de
                                                    passe</label>
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Au moins 8 caractères</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmation" class="small fw-semibold">Confirmer
                                                    le mot de passe</label>
                                                <input type="password" name="password_confirmation"
                                                    class="form-control" id="password_confirmation">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pf-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Assurez-vous de bien retenir votre nouveau mot de passe. Vous serez déconnecté
                                        après le changement.
                                    </div>

                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-warning px-4">
                                            <i class="bi bi-shield-lock"></i> Changer le mot de passe
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <style>
        .pf-sticky {
            position: sticky;
            top: 80px;
        }

        .pf-summary-card {
            background: #fff;
            border: 1px solid #e3e8e5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(15, 30, 20, 0.06);
        }

        .pf-summary-accent {
            height: 5px;
            background: linear-gradient(90deg, #0d47a1, #1e88e5);
        }

        .pf-summary-body {
            padding: 30px 24px;
            text-align: center;
        }

        .pf-avatar {
            width: 84px;
            height: 84px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d47a1, #1e88e5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: .5px;
            box-shadow: 0 4px 10px rgba(27, 94, 32, 0.25);
        }

        .pf-name {
            font-size: 19px;
            font-weight: 800;
            color: #1f2a24;
            margin-bottom: 10px;
        }

        .pf-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .pf-role-badge-sm {
            padding: 3px 10px;
            font-size: 10.5px;
        }

        .pf-role-admin {
            background: #fde3e1;
            color: #b3401d;
        }

        .pf-role-super_admin {
            background: #fde3e1;
            color: #b3401d;
        }

        .pf-role-manager {
            background: #fdecd2;
            color: #97590b;
        }

        .pf-role-user {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .pf-info-list {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px dashed #e3e8e5;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
        }

        .pf-info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12.5px;
            color: #4a564e;
        }

        .pf-info-row i {
            color: #1565c0;
            font-size: 14px;
            width: 18px;
            text-align: center;
        }

        .pf-card {
            border: 1px solid #e3e8e5;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(15, 30, 20, 0.06);
        }

        .pf-nav-pills .nav-link {
            font-size: 13px;
            font-weight: 600;
            color: #4a564e;
            border-radius: 20px;
            padding: 8px 18px;
            margin-right: 6px;
        }

        .pf-nav-pills .nav-link i {
            margin-right: 4px;
        }

        .pf-nav-pills .nav-link.active {
            background: #0d47a1;
            color: #fff;
        }

        .pf-section {
            padding: 16px 18px;
            background: #f7f9f8;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .pf-section-title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: #0d47a1;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pf-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px dotted #dde3e0;
            font-size: 13px;
        }

        .pf-detail-row-last {
            border-bottom: none;
        }

        .pf-detail-label {
            color: #6b7a72;
            font-weight: 600;
        }

        .pf-detail-value {
            color: #1f2a24;
            font-weight: 600;
        }

        .pf-warning {
            padding: 12px 16px;
            background: #fdecd2;
            color: #97590b;
            border-radius: 8px;
            font-size: 12.5px;
        }

        .pf-warning i {
            margin-right: 6px;
        }
    </style>
@endsection
