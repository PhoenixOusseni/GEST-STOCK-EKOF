@extends('layouts.master')
@section('title', 'Gestion des Articles')
@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Gestion des articles</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Gestion des articles</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_articles.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i>&nbsp; Retour
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Ajouter des articles</h5>
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-ligne">
                                <i class="bi bi-plus-circle"></i>&nbsp; Ajouter une ligne
                            </button>
                        </div>
                        <hr>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('gestions_articles.store') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="articles-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="col-designation">Désignation <span class="text-danger">*</span></th>
                                            <th class="col-money">Prix Achat <span class="text-danger">*</span></th>
                                            <th class="col-money">Prix Vente <span class="text-danger">*</span></th>
                                            <th class="col-qty">Stock</th>
                                            <th class="col-qty">Seuil</th>
                                            <th class="col-action"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="articles-body">
                                        <tr class="article-row">
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="designation[]" required placeholder="Désignation">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" name="prix_achat[]" step="0.01" min="0" required placeholder="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" name="prix_vente[]" step="0.01" min="0" required placeholder="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" name="stock[]" value="0" min="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" name="seuil[]" value="10" min="0">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-ligne" title="Supprimer la ligne">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i>&nbsp; Enregistrer les articles
                                </button>
                                <a href="{{ route('gestions_articles.index') }}" class="btn btn-secondary">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const rowTemplate = `
            <tr class="article-row">
                <td><input type="text" class="form-control form-control-sm" name="designation[]" required placeholder="Désignation"></td>
                <td><input type="number" class="form-control form-control-sm" name="prix_achat[]" step="0.01" min="0" required placeholder="0"></td>
                <td><input type="number" class="form-control form-control-sm" name="prix_vente[]" step="0.01" min="0" required placeholder="0"></td>
                <td><input type="number" class="form-control form-control-sm" name="stock[]" value="0" min="0"></td>
                <td><input type="number" class="form-control form-control-sm" name="seuil[]" value="10" min="0"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-ligne" title="Supprimer la ligne">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;

        document.getElementById('btn-add-ligne').addEventListener('click', function () {
            document.getElementById('articles-body').insertAdjacentHTML('beforeend', rowTemplate);
            updateRemoveButtons();
        });

        document.getElementById('articles-body').addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-ligne');
            if (!btn) return;
            const rows = document.querySelectorAll('.article-row');
            if (rows.length > 1) {
                btn.closest('.article-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.article-row');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-remove-ligne');
                btn.disabled = rows.length === 1;
            });
        }

        updateRemoveButtons();
    </script>
@endsection
