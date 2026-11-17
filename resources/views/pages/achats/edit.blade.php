@extends('layouts.master')

@section('content')

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Modifier l'Achat {{ $achat->numero_achat }}</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_achats.index') }}">Achats</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_achats.show', $achat->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Modification de l'Achat</h5>
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

                        <form action="{{ route('gestions_achats.update', $achat->id) }}" method="POST" data-stock-url="{{ route('ventes.getStock') }}">
                            @csrf
                            @method('PUT')

                            <!-- Informations générales -->
                            <div class="mb-4" style="background: rgb(232, 240, 243); padding: 15px; border-radius: 5px;">
                                <h6 class="mb-3"><i class="bi bi-info-circle"></i> Informations de l'Achat</h6>
                                @include('partials.depot-select', ['depotCourant' => old('depot_id', optional($achat->details->first())->depot_id)])
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label class="small">Numéro</label>
                                        <input type="text" class="form-control" value="{{ $achat->numero_achat }}" disabled>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="date_achat" class="small">Date d'Achat <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_achat') is-invalid @enderror"
                                            id="date_achat" name="date_achat"
                                            value="{{ old('date_achat', $achat->date_achat->format('Y-m-d')) }}" required>
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
                                                    {{ old('fournisseur_id', $achat->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
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
                                            value="{{ old('designation', $achat->designation) }}"
                                            placeholder="Description de l'achat...">
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="small">Déjà payé</label>
                                        <input type="text" class="form-control bg-light text-success fw-bold" readonly
                                            value="{{ number_format($achat->montant_paye, 0, ',', ' ') }} FCFA">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="small">Reste à Payer</label>
                                        <input type="text" class="form-control bg-light fw-bold {{ $achat->reste_a_payer > 0 ? 'text-danger' : 'text-success' }}" readonly
                                            value="{{ number_format($achat->reste_a_payer, 0, ',', ' ') }} FCFA">
                                    </div>
                                </div>
                            </div>

                            <!-- Articles achetés -->
                            <div class="mb-4" style="background: rgb(243, 246, 248); padding: 15px; border-radius: 5px;">
                                <h6 class="mb-3"><i class="bi bi-bag"></i> Articles Achetés</h6>
                                <div id="articles-container">
                                    @foreach ($achat->details as $index => $detail)
                                        <div class="article-row mb-3 p-3"
                                            style="background: white; border: 1px solid #dee2e6; border-radius: 5px;">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="small">Article <span class="text-danger">*</span></label>
                                                    <select class="form-select select2-article article-select"
                                                        name="articles[{{ $index }}][article_id]" required>
                                                        <option value="">-- Sélectionner --</option>
                                                        @foreach ($articles as $article)
                                                            <option value="{{ $article->id }}"
                                                                data-prix="{{ $article->prix_achat }}"
                                                                {{ $detail->article_id == $article->id ? 'selected' : '' }}>
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
                                                        name="articles[{{ $index }}][quantite]"
                                                        value="{{ $detail->quantite }}" step="0.01" min="0.01" required>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="small">Prix Unitaire <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control prix-unitaire-input"
                                                        name="articles[{{ $index }}][prix_unitaire]"
                                                        value="{{ $detail->prix_unitaire }}" step="0.01" min="0" required>
                                                </div>
                                                <div class="col-auto mb-3 d-flex align-items-end gap-1">
                                                    <button type="button" class="btn btn-sm btn-danger remove-article">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    @if ($loop->last)
                                                        <button type="button" class="btn btn-sm btn-success add-article">
                                                            <i class="bi bi-plus-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i>&nbsp; Modifier l'Achat
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
                            <strong id="articles-count">{{ $achat->details->count() }}</strong>
                        </div>
                    </div>
                    <div class="side-stat is-red">
                        <span class="side-stat-icon"><i class="bi bi-cash-stack"></i></span>
                        <div>
                            <p>Montant total</p>
                            <strong id="total-display">{{ number_format($achat->montant_total, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        let articleIndex = {{ $achat->details->count() }};

        function initSelect2Fournisseur(el) {
            $(el || '.select2-fournisseur').select2({
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
            $(el || '.select2-article').select2({
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
            $(el || '.select2-depot').select2({
                width: '100%',
                placeholder: 'Sélectionner un magazin...',
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

        function updateReste() {
            const total = parseFloat(document.getElementById('total-display').textContent.replace(/\s/g, '').replace('FCFA','').replace(',','.')) || 0;
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

        // Calculer et afficher le montant total et le nombre d'articles
        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.article-row').forEach(function(row) {
                const qte = parseFloat(row.querySelector('.quantite-input').value) || 0;
                const prix = parseFloat(row.querySelector('.prix-unitaire-input').value) || 0;
                total += qte * prix;
            });
            document.getElementById('total-display').textContent = total.toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('articles-count').textContent = document.querySelectorAll('.article-row').length;
            updateReste();
        }

        // Ajouter un article
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('add-article') && !e.target.closest('.add-article')) return;
            const container = document.getElementById('articles-container');
            const lastRow = container.querySelector('.article-row:last-child');
            if (lastRow) {
                const addBtn = lastRow.querySelector('.add-article');
                if (addBtn) addBtn.remove();
            }
            const newArticle = document.createElement('div');
            newArticle.className = 'article-row mb-3 p-3';
            newArticle.style.cssText = 'background: white; border: 1px solid #dee2e6; border-radius: 5px;';
            newArticle.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small">Article <span class="text-danger">*</span></label>
                        <select class="form-select select2-article article-select" name="articles[${articleIndex}][article_id]" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach ($articles as $article)
                                <option value="{{ $article->id }}" data-prix="{{ $article->prix_achat }}">
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
                               name="articles[${articleIndex}][quantite]" placeholder="0" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small">Prix Unitaire <span class="text-danger">*</span></label>
                        <input type="number" class="form-control prix-unitaire-input"
                               name="articles[${articleIndex}][prix_unitaire]" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="col-auto mb-3 d-flex align-items-end gap-1">
                        <button type="button" class="btn btn-sm btn-danger remove-article">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-success add-article">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newArticle);
            articleIndex++;
            initSelect2Article(newArticle.querySelector('.select2-article'));
            initSelect2Depot(newArticle.querySelector('.select2-depot'));
            addArticleEvents(newArticle);
            updateTotal();
        });

        // Supprimer un article
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-article') || e.target.closest('.remove-article')) {
                const container = document.getElementById('articles-container');
                const articlesCount = container.querySelectorAll('.article-row').length;
                if (articlesCount > 1) {
                    const row = e.target.closest('.article-row');
                    const hadAddBtn = !!row.querySelector('.add-article');
                    $(row).find('.select2-article, .select2-depot').select2('destroy');
                    row.remove();
                    updateTotal();
                    if (hadAddBtn) {
                        const newLastRow = container.querySelector('.article-row:last-child');
                        if (newLastRow && !newLastRow.querySelector('.add-article')) {
                            const btnGroup = newLastRow.querySelector('.col-auto');
                            if (btnGroup) {
                                const addBtn = document.createElement('button');
                                addBtn.type = 'button';
                                addBtn.className = 'btn btn-sm btn-success add-article';
                                addBtn.innerHTML = '<i class="bi bi-plus-circle"></i>';
                                btnGroup.appendChild(addBtn);
                            }
                        }
                    }
                } else {
                    alert('Vous devez avoir au moins un article dans l\'achat.');
                }
            }
        });

        document.getElementById('montant_paye').addEventListener('input', updateReste);

        $(document).ready(function() {
            initSelect2Fournisseur();
            initSelect2Article();
            initSelect2Depot();
            document.querySelectorAll('.article-row').forEach(row => addArticleEvents(row));
            updateTotal();
        });
    </script>
@endsection
