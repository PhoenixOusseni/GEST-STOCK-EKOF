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
            <a href="{{ route('gestions_fournisseurs.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i>&nbsp; Retour
            </a>
        </div>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ajouter un Fournisseur</h5>
                        <hr>
                        <form action="{{ route('gestions_fournisseurs.store') }}" method="POST">
                            @csrf

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Ajouter un ou plusieurs fournisseurs</h6>
                                    <small class="text-muted">Ajoutez des lignes et cliquez sur Enregistrer.</small>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="addFournisseurRow">
                                    <i class="bi bi-plus-lg"></i>&nbsp; Ajouter une ligne
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="fournisseursTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Raison Sociale</th>
                                            <th>Nom</th>
                                            <th>Téléphone</th>
                                            <th style="width: 80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="row-index">1</td>
                                            <td>
                                                <input type="text" class="form-control" name="raison_sociale[]" placeholder="Nom de l'entreprise">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="nom[]" placeholder="Prénom et Nom">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="telephone[]" placeholder="+xxx xxx xxx xxx">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-fournisseur-row" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i>&nbsp; Enregistrer les fournisseurs
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function updateFournisseurRowIndices() {
            document.querySelectorAll('#fournisseursTable tbody tr').forEach((row, index) => {
                const indexCell = row.querySelector('.row-index');
                if (indexCell) {
                    indexCell.textContent = index + 1;
                }
                const removeButton = row.querySelector('.remove-fournisseur-row');
                if (removeButton) {
                    removeButton.disabled = document.querySelectorAll('#fournisseursTable tbody tr').length === 1;
                }
            });
        }

        function createFournisseurRow() {
            const tbody = document.querySelector('#fournisseursTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="row-index"></td>
                <td>
                    <input type="text" class="form-control" name="raison_sociale[]" placeholder="Nom de l'entreprise">
                </td>
                <td>
                    <input type="text" class="form-control" name="nom[]" placeholder="Prénom et Nom">
                </td>
                <td>
                    <input type="text" class="form-control" name="telephone[]" placeholder="+xxx xxx xxx xxx">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-fournisseur-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            updateFournisseurRowIndices();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addFournisseurRow').addEventListener('click', createFournisseurRow);

            document.querySelector('#fournisseursTable tbody').addEventListener('click', function(event) {
                const button = event.target.closest('.remove-fournisseur-row');
                if (!button) return;

                const tbody = document.querySelector('#fournisseursTable tbody');
                if (tbody.querySelectorAll('tr').length <= 1) {
                    return;
                }

                button.closest('tr').remove();
                updateFournisseurRowIndices();
            });

            updateFournisseurRowIndices();
        });
    </script>
@endsection

