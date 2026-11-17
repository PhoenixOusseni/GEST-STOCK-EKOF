@extends('layouts.master')

@section('content')

    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h3>Modifier la Vente {{ $vente->numero_vente }}</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_ventes.index') }}">Ventes</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_ventes.show', $vente) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left"></i>&nbsp; Retour
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Modifier la Vente</h5>
                        <hr>
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <strong>Erreur!</strong> Veuillez corriger les
                                erreurs suivantes:
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('gestions_ventes.update', $vente) }}" method="POST" data-stock-url="{{ route('ventes.getStock') }}" data-stock-guard="1">
                            @csrf
                            @method('PUT')

                            <!-- Informations g�n�rales -->
                            <div class="mb-4" style="background: rgb(232, 240, 243); padding: 15px; border-radius: 5px;">
                                <h6 class="mb-3"><i class="bi bi-info-circle"></i> Informations de la Vente</h6>
                                @include('partials.depot-select', ['depotCourant' => old('depot_id', optional($vente->details->first())->depot_id)])
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="numero_vente" class="small">Numéro de Vente</label>
                                        <input type="text" class="form-control" id="numero_vente"
                                            value="{{ $vente->numero_vente }}" disabled>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="date_vente" class="small">Date de Vente <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_vente') is-invalid @enderror"
                                            id="date_vente" name="date_vente"
                                            value="{{ old('date_vente', $vente->date_vente->format('Y-m-d')) }}" required>
                                        @error('date_vente')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="client_id" class="small">Client</label>
                                        <select class="form-select select2-client @error('client_id') is-invalid @enderror"
                                            id="client_id" name="client_id">
                                            <option value="">-- Sans client --</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ old('client_id', $vente->client_id) == $client->id ? 'selected' : '' }}>
                                                    {{ $client->raison_sociale ? $client->raison_sociale : $client->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="small d-block">Type de Vente <span class="text-danger">*</span></label>
                                        <div class="btn-group w-100" role="group" aria-label="Type de vente">
                                            <input type="radio" class="btn-check" name="type_vente" id="type_vente_detail"
                                                value="detail" {{ old('type_vente', $vente->type_vente) == 'detail' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="type_vente_detail">Détail</label>

                                            <input type="radio" class="btn-check" name="type_vente" id="type_vente_gros"
                                                value="gros" {{ old('type_vente', $vente->type_vente) == 'gros' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="type_vente_gros">Gros</label>
                                        </div>
                                        @error('type_vente')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="small">Déjà payé</label>
                                        <input type="text" class="form-control bg-light text-success fw-bold" readonly
                                            value="{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA ({{ $vente->paiements->count() }} versement(s))">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small">Reste à Payer</label>
                                        <input type="text" class="form-control bg-light fw-bold {{ $vente->reste_a_payer > 0 ? 'text-danger' : 'text-success' }}" readonly
                                            value="{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA">
                                    </div>
                                </div>
                            </div>

                            <!-- Articles -->
                            <div class="mb-4" style="background: rgb(243, 246, 248); padding: 15px; border-radius: 5px;">
                                <h6 class="mb-3"><i class="bi bi-bag"></i> Articles à Vendre</h6>
                                <div id="articles-container">
                                    @foreach ($vente->details as $index => $detail)
                                        <div class="article-row mb-3 p-3"
                                            style="background: white; border: 1px solid #dee2e6; border-radius: 5px;">
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label class="small">Article <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select select2-article article-select"
                                                        name="articles[{{ $index }}][article_id]" required>
                                                        <option value="">-- Sélectionner --</option>
                                                        @foreach ($articles as $article)
                                                            <option value="{{ $article->id }}"
                                                                data-prix="{{ $article->prix_vente }}"
                                                                {{ $detail->article_id == $article->id ? 'selected' : '' }}>
                                                                {{ $article->designation }} ({{ $article->code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="small">Quantité <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control quantite-input"
                                                        name="articles[{{ $index }}][quantite]"
                                                        value="{{ $detail->quantite }}" step="0.01" min="0.01"
                                                        required>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="small">Prix Vente <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control prix-vente-input"
                                                        name="articles[{{ $index }}][prix_vente]"
                                                        value="{{ $detail->prix_vente }}" step="0.01" min="0"
                                                        required>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="small">Stock Dispo.</label>
                                                    <input type="text" class="form-control stock-disponible" readonly
                                                        placeholder="0">
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
                                    <i class="bi bi-save"></i>&nbsp; Modifier la Vente
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
                            <strong id="articles-count">{{ $vente->details->count() }}</strong>
                        </div>
                    </div>
                    <div class="side-stat is-red">
                        <span class="side-stat-icon"><i class="bi bi-cash-stack"></i></span>
                        <div>
                            <p>Montant total</p>
                            <strong id="total-display">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        let articleIndex = {{ $vente->details->count() }};

        // Initialiser Select2
        function initSelect2Client(el) {
            $(el || '.select2-client').select2({
                width: '100%',
                placeholder: 'Rechercher un client...',
                allowClear: true,
                ajax: {
                    url: '{{ route('clients.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(c => ({
                                id: c.id,
                                text: c.raison_sociale || c.nom
                            }))
                        };
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
                    url: '{{ route('articles.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(a => ({
                                id: a.id,
                                text: a.designation + ' (' + a.code + ')',
                                prix: a.prix_vente
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
                    url: '{{ route('depots.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(d => ({
                                id: d.id,
                                text: d.designation
                            }))
                        };
                    }
                }
            });
        }

        // Ajouter un nouvel article
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('add-article') && !e.target.closest('.add-article')) return;
            const container = document.getElementById('articles-container');
            // Retirer add-article du dernier article actuel
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
                    <div class="col-md-3 mb-3">
                        <label class="small">Article <span class="text-danger">*</span></label>
                        <select class="form-select select2-article article-select" name="articles[${articleIndex}][article_id]" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach ($articles as $article)
                                <option value="{{ $article->id }}" data-prix="{{ $article->prix_vente }}">
                                    {{ $article->designation }} ({{ $article->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small">Quantité <span class="text-danger">*</span></label>
                        <input type="number" class="form-control quantite-input"
                               name="articles[${articleIndex}][quantite]" placeholder="0" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small">Prix Vente <span class="text-danger">*</span></label>
                        <input type="number" class="form-control prix-vente-input"
                               name="articles[${articleIndex}][prix_vente]" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label class="small">Stock Dispo.</label>
                        <input type="text" class="form-control stock-disponible" readonly placeholder="0">
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
            // Initialiser Select2 sur la nouvelle ligne
            initSelect2Article(newArticle.querySelector('.select2-article'));
            initSelect2Depot(newArticle.querySelector('.select2-depot'));
            // Ajouter les événements aux nouveaux champs
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
                    // Si la ligne supprimée avait le bouton add-article, l'ajouter à la nouvelle dernière ligne
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
                    alert('Vous devez avoir au moins un article dans la vente.');
                }
            }
        });

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
                const prix = parseFloat(row.querySelector('.prix-vente-input').value) || 0;
                total += qte * prix;
            });
            document.getElementById('total-display').textContent = total.toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('articles-count').textContent = document.querySelectorAll('.article-row').length;
            updateReste();
        }

        // Fonction pour ajouter les événements à une ligne d'article
        function addArticleEvents(row) {
            const articleSelect = row.querySelector('.article-select');
            const depotSelect = row.querySelector('.depot-select');
            const stockInput = row.querySelector('.stock-disponible');
            const prixVenteInput = row.querySelector('.prix-vente-input');
            const quantiteInput = row.querySelector('.quantite-input');

            // Charger le prix avec Select2
            $(articleSelect).on('select2:select', function(e) {
                const data = e.params.data;
                if (data.prix) prixVenteInput.value = data.prix;
                checkStock(row);
                updateTotal();
            });

            // Vérifier le stock avec Select2
            $(depotSelect).on('select2:select', function() {
                checkStock(row);
            });

            // Mise à jour du total à la saisie
            prixVenteInput.addEventListener('input', updateTotal);
            quantiteInput.addEventListener('input', updateTotal);

            // Charger le stock initial si les deux champs sont remplis
            if (articleSelect.value && document.getElementById('depot_id') && document.getElementById('depot_id').value) {
                checkStock(row);
            }
        }

        // V�rifier le stock disponible
        function checkStock(row) {
            const articleId = row.querySelector('.article-select').value;
            const depotId = document.getElementById('depot_id') ? document.getElementById('depot_id').value : '';
            const stockInput = row.querySelector('.stock-disponible');

            if (articleId && depotId) {
                fetch('{{ route('ventes.getStock') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            article_id: articleId,
                            depot_id: depotId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            stockInput.value = data.quantite_disponible;
                            stockInput.classList.remove('is-invalid');
                            stockInput.classList.add('is-valid');
                        } else {
                            stockInput.value = '0';
                            stockInput.classList.remove('is-valid');
                            stockInput.classList.add('is-invalid');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        stockInput.value = 'Erreur';
                    });
            }
        }

        document.getElementById('montant_paye').addEventListener('input', updateReste);

        // Initialiser au chargement
        $(document).ready(function() {
            initSelect2Client();
            initSelect2Article();
            initSelect2Depot();

            // Ajouter les événements aux lignes existantes
            document.querySelectorAll('.article-row').forEach(row => {
                addArticleEvents(row);
            });
            updateTotal();
        });
    </script>
@endsection
