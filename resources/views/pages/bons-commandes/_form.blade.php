@php
    $lignes = old('articles');
    if ($lignes === null) {
        $lignes = isset($bon)
            ? $bon->details->map(fn ($detail) => [
                'article_id' => $detail->article_id,
                'quantite' => $detail->quantite,
                'prix_vente' => $detail->prix_vente,
            ])->all()
            : [['article_id' => '', 'quantite' => '', 'prix_vente' => '']];
    }
    $depotCourant = old('depot_id', isset($bon) ? optional($bon->details->first())->depot_id : null);
@endphp

<form action="{{ $formAction }}" method="POST" id="bon-form" data-stock-url="{{ route('ventes.getStock') }}" data-line-count="{{ count($lignes) }}">
    @csrf
    @if (($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="mb-4" style="background: rgb(232, 240, 243); padding: 15px; border-radius: 5px;">
        <h6 class="mb-3"><i class="bi bi-info-circle"></i> Informations du bon de commande</h6>
        <p class="small text-muted">Un article sans stock bloque le bon. S'il a du stock, le bon est enregistré et le stock du magazin reste intact.</p>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="depot_id" class="small fw-semibold">Magazin <span class="text-danger">*</span></label>
                @if ($depotActif)
                    <input type="hidden" name="depot_id" id="depot_id" value="{{ $depotActif->id }}">
                    <input type="text" class="form-control" value="{{ $depotActif->designation }}" readonly>
                    <small class="text-muted">Les quantités et le stock concernent ce magazin.</small>
                @else
                    <select class="form-select @error('depot_id') is-invalid @enderror" id="depot_id" name="depot_id" required>
                        <option value="">-- Sélectionner le magazin d'abord --</option>
                        @foreach ($depots as $depot)
                            <option value="{{ $depot->id }}" @selected((string) old('depot_id', $depotCourant ?? '') === (string) $depot->id)>
                                {{ $depot->designation }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Choisissez le magazin avant les articles.</small>
                @endif
                @error('depot_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="small d-block">Type de vente <span class="text-danger">*</span></label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="type_vente" id="type_vente_detail" value="detail"
                        @checked(old('type_vente', isset($bon) ? $bon->type_vente : 'detail') === 'detail')>
                    <label class="btn btn-outline-primary" for="type_vente_detail">Détail</label>
                    <input type="radio" class="btn-check" name="type_vente" id="type_vente_gros" value="gros"
                        @checked(old('type_vente', isset($bon) ? $bon->type_vente : 'detail') === 'gros')>
                    <label class="btn btn-outline-primary" for="type_vente_gros">Gros</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="small">Numéro</label>
                <input type="text" class="form-control" value="{{ isset($bon) ? $bon->numero_bon : 'Auto-généré' }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label for="date_commande" class="small">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('date_commande') is-invalid @enderror" id="date_commande" name="date_commande"
                    value="{{ old('date_commande', isset($bon) ? $bon->date_commande->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('date_commande')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="client_id" class="small">Client</label>
                <select class="form-select select2-client @error('client_id') is-invalid @enderror" id="client_id" name="client_id">
                    <option value="">-- Sans client --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected((string) old('client_id', isset($bon) ? $bon->client_id : '') === (string) $client->id)>
                            {{ $client->raison_sociale ?? $client->nom }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-4" style="background: rgb(243, 246, 248); padding: 15px; border-radius: 5px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0"><i class="bi bi-bag"></i> Articles</h6>
            <button type="button" id="add-article" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle"></i>
            </button>
        </div>
        <div id="articles-container">
            @foreach ($lignes as $index => $ligne)
                <div class="article-row mb-3 p-3" style="background: white; border: 1px solid #dee2e6; border-radius: 5px;">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="small">Article <span class="text-danger">*</span></label>
                            <select class="form-select select2-article article-select" name="articles[{{ $index }}][article_id]" required>
                                <option value="" disabled @selected(empty($ligne['article_id']))>-- Sélectionner --</option>
                                @foreach ($articles as $article)
                                    <option value="{{ $article->id }}" data-prix="{{ $article->prix_vente }}" @selected((string) ($ligne['article_id'] ?? '') === (string) $article->id)>
                                        {{ $article->designation }} ({{ $article->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="small">Quantité <span class="text-danger">*</span></label>
                            <input type="number" class="form-control quantite-input" name="articles[{{ $index }}][quantite]"
                                value="{{ $ligne['quantite'] ?? '' }}" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="small">Prix de vente <span class="text-danger">*</span></label>
                            <input type="number" class="form-control prix-vente-input" name="articles[{{ $index }}][prix_vente]"
                                value="{{ $ligne['prix_vente'] ?? '' }}" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="small">Stock magazin</label>
                            <input type="text" class="form-control stock-disponible" readonly placeholder="0">
                        </div>
                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger remove-article">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i>&nbsp; Enregistrer le bon
    </button>
</form>
