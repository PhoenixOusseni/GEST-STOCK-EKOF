@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Gestion des Fournisseurs</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Gestion des Fournisseurs</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_fournisseurs.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> &nbsp; Retour
                </a>
                <form action="{{ route('gestions_fournisseurs.destroy', $fournisseurFinds->id) }}" method="POST"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> &nbsp; Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Modification du Fournisseur {{ $fournisseurFinds->code }}</h5>
                        <form action="{{ route('gestions_fournisseurs.update', $fournisseurFinds->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Code et Raison Sociale -->
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label for="code" class="small">Code Fournisseur</label>
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ $fournisseurFinds->code }}" disabled>
                                </div>
                                <div class="col-md-3 mb-3" id="raison_sociale_field">
                                    <label for="raison_sociale" class="small">Raison Sociale</label>
                                    <input type="text" class="form-control" id="raison_sociale" name="raison_sociale"
                                        value="{{ $fournisseurFinds->raison_sociale }}">
                                </div>
                                <div class="col-md-3 mb-3" id="nom_field">
                                    <label for="nom" class="small">Nom du Fournisseur</label>
                                    <input type="text" class="form-control" id="nom" name="nom"
                                        value="{{ $fournisseurFinds->nom }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="telephone" class="small">Téléphone</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone"
                                        value="{{ $fournisseurFinds->telephone }}" placeholder="+xxx xxx xxx xxx">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-pencil-square"></i>&nbsp; Modifier le Fournisseur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<script>
    function toggleFields() {
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const raisonSocialeField = document.getElementById('raison_sociale_field');
        const nomField = document.getElementById('nom_field');
        const raisonSocialeInput = document.getElementById('raison_sociale');
        const nomInput = document.getElementById('nom');

        typeRadios.forEach(radio => {
            if (radio.checked) {
                if (radio.value === 'personne') {
                    // Si personne physique: masquer raison_sociale, afficher nom
                    raisonSocialeField.style.display = 'none';
                    raisonSocialeInput.removeAttribute('required');
                    nomField.style.display = 'block';
                    nomInput.setAttribute('required', 'required');
                } else if (radio.value === 'structure') {
                    // Si structure: afficher raison_sociale, masquer nom
                    raisonSocialeField.style.display = 'block';
                    raisonSocialeInput.setAttribute('required', 'required');
                    nomField.style.display = 'none';
                    nomInput.removeAttribute('required');
                }
            }
        });
    }

    // Initialiser l'affichage au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
    });
</script>
