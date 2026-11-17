@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <h1>Information de la société</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Information de la société</li>
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

        @forelse ($entetes as $item)
            <div class="row g-4">
                <!-- Aperçu de l'en-tête -->
                <div class="col-lg-4">
                    <div class="eh-sticky">
                        <div class="eh-preview-card">
                            <div class="eh-preview-accent"></div>
                            <div class="eh-preview-body">
                                <div class="eh-preview-logo" id="ehLogoPreview">
                                    @if ($item->logo)
                                        <img src="{{ asset('storage/' . $item->logo) }}" alt="Logo">
                                    @else
                                        <i class="bi bi-building"></i>
                                    @endif
                                </div>
                                <div class="eh-preview-title">{{ $item->titre ?: 'Nom de la société' }}</div>
                                @if ($item->sous_titre)
                                    <div class="eh-preview-subtitle">{{ $item->sous_titre }}</div>
                                @endif
                                <div class="eh-preview-contact">
                                    @if ($item->telephone)
                                        <span><i class="bi bi-telephone"></i> {{ $item->telephone }}</span>
                                    @endif
                                    @if ($item->email)
                                        <span><i class="bi bi-envelope"></i> {{ $item->email }}</span>
                                    @endif
                                    @if ($item->adresse)
                                        <span><i class="bi bi-geo-alt"></i> {{ $item->adresse }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <p class="eh-preview-caption">
                            <i class="bi bi-info-circle"></i>
                            Aperçu de l'en-tête utilisé sur les factures et documents imprimés.
                        </p>
                    </div>
                </div>

                <!-- Formulaire des informations -->
                <div class="col-lg-8">
                    <div class="card eh-card">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Informations générales</h5>
                            <p class="text-muted small mb-4">Ces informations apparaissent sur l'en-tête de tous vos
                                documents (factures, bons, reçus).</p>

                            <form method="POST" action="{{ route('entetes.update', $item->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="eh-section">
                                    <div class="eh-section-title"><i class="bi bi-badge-tm"></i> Identité</div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="titre" class="small fw-semibold">Nom / Intitulé</label>
                                            <input name="titre" type="text" class="form-control" id="titre"
                                                value="{{ old('titre', $item->titre) }}" placeholder="Ex : SAFRECO-GSM">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="logo" class="small fw-semibold">Logo</label>
                                            <input name="logo" type="file" class="form-control" id="logo"
                                                accept="image/*">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="sous_titre" class="small fw-semibold">Slogan / Sous-titre</label>
                                            <input name="sous_titre" type="text" class="form-control" id="sous_titre"
                                                value="{{ old('sous_titre', $item->sous_titre) }}"
                                                placeholder="Ex : Votre partenaire de confiance">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="description" class="small fw-semibold">Description</label>
                                            <textarea name="description" class="form-control" id="description" rows="3"
                                                placeholder="Courte présentation de l'entreprise...">{{ old('description', $item->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="eh-section">
                                    <div class="eh-section-title"><i class="bi bi-geo-alt"></i> Coordonnées</div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="telephone" class="small fw-semibold">Téléphone</label>
                                            <input name="telephone" type="text" class="form-control" id="telephone"
                                                value="{{ old('telephone', $item->telephone) }}"
                                                placeholder="Ex : 70 00 00 00">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="small fw-semibold">Email</label>
                                            <input name="email" type="email" class="form-control" id="email"
                                                value="{{ old('email', $item->email) }}"
                                                placeholder="contact@societe.com">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="adresse" class="small fw-semibold">Adresse</label>
                                            <input name="adresse" type="text" class="form-control" id="adresse"
                                                value="{{ old('adresse', $item->adresse) }}"
                                                placeholder="Ville, quartier, secteur...">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-2">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save"></i>&nbsp; Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucune information de société enregistrée pour le moment.
            </div>
        @endforelse
    </section>
@endsection

@section('scripts')
    <style>
        .eh-sticky {
            position: sticky;
            top: 80px;
        }

        .eh-preview-card {
            background: #fff;
            border: 1px solid #e3e8e5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(15, 30, 20, 0.06);
        }

        .eh-preview-accent {
            height: 5px;
            background: linear-gradient(90deg, #0d47a1, #1e88e5);
        }

        .eh-preview-body {
            padding: 28px 22px;
            text-align: center;
        }

        .eh-preview-logo {
            width: 76px;
            height: 76px;
            margin: 0 auto 14px;
            border-radius: 10px;
            background: #eef1ef;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 28px;
            color: #6b7a72;
        }

        .eh-preview-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .eh-preview-title {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #1f2a24;
        }

        .eh-preview-subtitle {
            font-size: 12.5px;
            font-weight: 600;
            color: #1565c0;
            margin-top: 3px;
        }

        .eh-preview-contact {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed #e3e8e5;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 11.5px;
            color: #6b7a72;
        }

        .eh-preview-caption {
            font-size: 11.5px;
            color: #8a978f;
            margin-top: 12px;
            text-align: center;
        }

        .eh-card {
            border: 1px solid #e3e8e5;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(15, 30, 20, 0.06);
        }

        .eh-section {
            padding: 16px 18px;
            background: #f7f9f8;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .eh-section-title {
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoInput = document.getElementById('logo');
            const preview = document.getElementById('ehLogoPreview');
            if (!logoInput || !preview) return;

            logoInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Logo">';
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
