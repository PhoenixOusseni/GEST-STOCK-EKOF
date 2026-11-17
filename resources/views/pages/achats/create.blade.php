@extends('layouts.master')

@section('content')

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Gestion des Achats</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_achats.index') }}">Achats</a></li>
                        <li class="breadcrumb-item active">Nouvel Achat</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_achats.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Enregistrer un Nouvel Achat</h5>
                        <hr>
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <strong>Erreur!</strong> Veuillez corriger les erreurs suivantes:
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('gestions_achats.store') }}" method="POST" data-stock-url="{{ route('ventes.getStock') }}">
                            @csrf

                            <!-- Informations générales -->
                            <div class="mb-4" style="background: rgb(232, 240, 243); padding: 15px; border-radius: 5px;">
                                <h6 class="mb-3"><i class="bi bi-info-circle"></i> Informations de l'Achat</h6>
                                @include('partials.depot-select')
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label class="small">Numéro</label>
                                        <input type="text" class="form-control" placeholder="Auto-généré" disabled>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="date_achat" class="small">Date d'Achat <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_achat') is-invalid @enderror"
                                            id="date_achat" name="date_achat"
                                            value="{{ old('date_achat', date('Y-m-d')) }}" required>
                                        @error('date_achat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fournisseur_id" class="small">Fournisseur</label>
                                        <select class="form-select select2-fournisseur @error('fournisseur_id') is-invalid @enderror"
                                            id="fournisseur_id" name="fournisseur_id">
                                            <option value="">-- Sans fournisseur --</option>
                                            @foreach ($fournisseurs as $fournisseur)
                                                <option value="{{ $fournisseur->id }}"
                                                    {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                    {{ $fournisseur->raison_sociale ?? $fournisseur->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('fournisseur_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="designation" class="small">Désignation / Observation</label>
                                        <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                            id="designation" name="designation"
                                            value="{{ old('designation') }}" placeholder="Description de l'achat...">
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="montant_paye" class="small">Premier versement <span class="text-muted small">(opt.)</span></label>
                                        <input type="number" class="form-control @error('montant_paye') is-invalid @enderror"
                                            id="montant_paye" name="montant_paye"
                                            value="{{ old('montant_paye', 0) }}" step="0.01" min="0" placeholder="0.00">
                                        @error('montant_paye')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="small">Reste à Payer</label>
                                        <input type="text" class="form-control bg-light fw-bold" id="reste_a_payer_display" readonly placeholder="0 FCFA">
                                    </div>
                                </div>
                            </div>

                            <!-- Articles achetés -->
                            <div class="mb-4" style="background: rgb(243, 246, 248); padding: 15px; border-radius: 5px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><i class="bi bi-bag"></i> Articles Achetés</h6>
                                    <button type="button" id="add-article" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                </div>
                                <div id="articles-container">
                                    <!-- Premier article -->
                                    <div class="article-row mb-3 p-3"
                                        style="background: white; border: 1px solid #dee2e6; border-radius: 5px;">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="small">Article <span class="text-danger">*</span></label>
                                                <select class="form-select select2-article article-select"
                                                    name="articles[0][article_id]" required>
                                                    <option value="" disabled>-- Sélectionner --</option>
                                                    @foreach ($articles as $article)
                                                        <option value="{{ $article->id }}"
                                                            data-prix="{{ $article->prix_achat }}">
                                                            {{ $article->designation }} ({{ $article->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="small">Stock magazin</label>
                                                <input type="text" class="form-control stock-disponible" readonly placeholder="0">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="small">Quantité <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control quantite-input"
                                                    name="articles[0][quantite]" placeholder="0" step="0.01" min="0.01" required>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="small">Prix Unitaire <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control prix-unitaire-input"
                                                    name="articles[0][prix_unitaire]" placeholder="0.00" step="0.01" min="0" required>
                                            </div>
                                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-article">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i>&nbsp; Enregistrer l'Achat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div style="position: sticky; top: 80px;">

                    <div class="side-stat is-blue">
                        <span class="side-stat-icon"><i class="bi bi-box-seam"></i></span>
                        <div>
                            <p>Articles</p>
                            <strong id="articles-count">0</strong>
                        </div>
                    </div>
                    <div class="side-stat is-red">
                        <span class="side-stat-icon"><i class="bi bi-cash-stack"></i></span>
                        <div>
                            <p>Montant total</p>
                            <strong id="total-display">0 FCFA</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script>
        let articleIndex = 1;

        function initSelect2Fournisseur(el) {
            $(el).select2({
                width: '100%',
                placeholder: 'Rechercher un fournisseur...',
                allowClear: true,
                ajax: {
                    url: '{{ route("fournisseurs.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { search: params.term }; },
                    processResults: function(data) {
                        return { results: data.map(f => ({ id: f.id, text: f.raison_sociale || f.nom })) };
                    }
                }
            });
        }

        function initSelect2Article(el) {
            $(el).select2({
                width: '100%',
                placeholder: 'Rechercher un article...',
                allowClear: true,
                ajax: {
                    url: '{{ route("articles.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { search: params.term }; },
                    processResults: function(data) {
                        return {
                            results: data.map(a => ({
                                id: a.id,
                                text: a.designation + ' (' + a.code + ')',
                                prix: a.prix_achat
                            }))
                        };
                    }
                }
            });
        }

        function initSelect2Depot(el) {
            $(el).select2({
                width: '100%',
                ajax: {
                    url: '{{ route("depots.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { search: params.term }; },
                    processResults: function(data) {
                        return { results: data.map(d => ({ id: d.id, text: d.designation })) };
                    }
                }
            });
        }

        function addArticleEvents(row) {
            const articleSelect = row.querySelector('.article-select');
            const prixUnitaireInput = row.querySelector('.prix-unitaire-input');
            const quantiteInput = row.querySelector('.quantite-input');

            $(articleSelect).on('select2:select', function(e) {
                const data = e.params.data;
                if (data.prix) prixUnitaireInput.value = data.prix;
                updateTotal();
            });

            prixUnitaireInput.addEventListener('input', updateTotal);
            quantiteInput.addEventListener('input', updateTotal);
        }

        const montantPayeInput = document.getElementById('montant_paye');
        let versementAuto = !montantPayeInput.value || parseFloat(montantPayeInput.value) === 0;

        function updateReste() {
            const total = parseFloat(document.getElementById('total-display').textContent.replace(/\s/g, '').replace('FCFA', '').replace(',', '.')) || 0;
            const paye = parseFloat(document.getElementById('montant_paye').value) || 0;
            const reste = Math.max(0, total - paye);
            document.getElementById('reste_a_payer_display').value = reste.toLocaleString('fr-FR') + ' FCFA';
            if (reste > 0) {
                document.getElementById('reste_a_payer_display').classList.add('text-danger');
                document.getElementById('reste_a_payer_display').classList.remove('text-success');
            } else {
                document.getElementById('reste_a_payer_display').classList.add('text-success');
                document.getElementById('reste_a_payer_display').classList.remove('text-danger');
            }
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.article-row').forEach(function(row) {
                const qte = parseFloat(row.querySelector('.quantite-input').value) || 0;
                const prix = parseFloat(row.querySelector('.prix-unitaire-input').value) || 0;
                total += qte * prix;
            });
            document.getElementById('total-display').textContent = total.toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('articles-count').textContent = document.querySelectorAll('.article-row').length;
            if (versementAuto) {
                montantPayeInput.value = String(Math.round(total * 100) / 100);
            }
            updateReste();
        }

        montantPayeInput.addEventListener('input', function () {
            versementAuto = false;
            updateReste();
        });

        document.getElementById('add-article').addEventListener('click', function() {
            const container = document.getElementById('articles-container');
            const firstRow = document.querySelector('.article-row');
            const newArticle = firstRow.cloneNode(true);

            // Supprimer les conteneurs Select2 clonés et restaurer les selects natifs
            newArticle.querySelectorAll('.select2-container').forEach(el => el.remove());
            newArticle.querySelectorAll('select').forEach(el => {
                el.style.display = '';
                el.removeAttribute('data-select2-id');
                el.selectedIndex = 0;
            });

            newArticle.querySelectorAll('input').forEach(input => {
                if (input.type !== 'hidden') input.value = '';
            });

            newArticle.querySelectorAll('[name]').forEach(field => {
                const name = field.getAttribute('name');
                field.setAttribute('name', name.replace(/\[\d+\]/, `[${articleIndex}]`));
            });

            container.appendChild(newArticle);
            articleIndex++;

            initSelect2Article(newArticle.querySelector('.select2-article'));
            initSelect2Depot(newArticle.querySelector('.select2-depot'));
            addArticleEvents(newArticle);
            updateTotal();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-article') || e.target.closest('.remove-article')) {
                const articlesCount = document.querySelectorAll('.article-row').length;
                if (articlesCount > 1) {
                    const row = e.target.closest('.article-row');
                    $(row).find('.select2-article, .select2-depot').select2('destroy');
                    row.remove();
                    updateTotal();
                } else {
                    alert('Vous devez avoir au moins un article dans l\'achat.');
                }
            }
        });

        $(document).ready(function() {
            initSelect2Fournisseur(document.querySelector('.select2-fournisseur'));
            initSelect2Article(document.querySelector('.select2-article'));
            initSelect2Depot(document.querySelector('.select2-depot'));
            addArticleEvents(document.querySelector('.article-row'));
            updateTotal();
        });
    </script>
@endsection
