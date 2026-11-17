@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-0">
                <h1>Détails de la Vente {{ $vente->numero_vente }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_ventes.index') }}">Ventes</a></li>
                        <li class="breadcrumb-item active">{{ $vente->numero_vente }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gestions_ventes.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                @if (\App\Support\Acces::peutModifier('facture'))
                <a href="{{ route('gestions_ventes.edit', $vente->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Modifier
                </a>
                @endif
                @if($vente->statut === 'non_solde' && \App\Support\Acces::peutCreer())
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSolderVente">
                    <i class="bi bi-cash-coin"></i> Solder
                </button>
                @endif
                <a href="#" class="btn btn-secondary btn-sm" onclick="window.open('{{ route('ventes.print', $vente->id) }}', '_blank')">
                    <i class="bi bi-printer"></i> Imprimer
                </a>
                @if (\App\Support\Acces::peutModifier('facture'))
                <form action="{{ route('gestions_ventes.destroy', $vente->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <section class="section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Informations Générales -->
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            Informations de la Vente
                            @if($vente->statut === 'solde')
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Soldé</span>
                            @else
                                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock"></i> Non soldé</span>
                            @endif
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width: 40%;">Numéro Vente:</td>
                                            <td><span class="badge bg-primary fs-6">{{ $vente->numero_vente }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Date Vente:</td>
                                            <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Client:</td>
                                            <td>
                                                @if($vente->client)
                                                    {{ $vente->client->raison_sociale ?? $vente->client->nom }}
                                                    @if($vente->client->telephone)
                                                        <br><small class="text-muted"><i class="bi bi-telephone"></i> {{ $vente->client->telephone }}</small>
                                                    @endif
                                                    @if($vente->client->email)
                                                        <br><small class="text-muted"><i class="bi bi-envelope"></i> {{ $vente->client->email }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted fst-italic">Sans client</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">Montant Total:</td>
                                            <td><span class="fs-5 fw-bold text-success">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Montant Payé:</td>
                                            <td><span class="fw-bold text-primary">{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Reste à Payer:</td>
                                            <td>
                                                <span class="fw-bold {{ $vente->reste_a_payer > 0 ? 'text-danger fs-5' : 'text-success' }}">
                                                    {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- D�tails des Articles -->
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Détails des Articles</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Code article</th>
                                        <th>Designation</th>
                                        <th>Magazin</th>
                                        <th class="text-end">Quantité</th>
                                        <th class="text-end">Prix Unitaire</th>
                                        <th class="text-end">Prix Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vente->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->article->code }}</td>
                                            <td>{{ $detail->article->designation }}</td>
                                            <td>{{ $detail->depot->designation }}</td>
                                            <td class="text-end">{{ number_format($detail->quantite, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($detail->prix_vente, 0, ',', ' ') }}</td>
                                            <td class="text-end">
                                                <strong>{{ number_format($detail->prix_total, 0, ',', ' ') }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total Général</th>
                                        <th class="text-end" colspan="2">
                                            <span class="fs-5 text-success">
                                                {{ number_format($vente->montant_total, 2, ',', ' ') }} FCFA
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des Paiements -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history"></i> Historique des Paiements
                                <span class="badge bg-secondary ms-2">{{ $vente->paiements->count() }}</span>
                            </h5>
                            @if($vente->statut === 'non_solde')
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSolderVente">
                                <i class="bi bi-plus-circle"></i> Nouveau paiement
                            </button>
                            @endif
                        </div>
                        @if($vente->paiements->isEmpty())
                            <div class="alert alert-info mb-0">Aucun paiement enregistré.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Mode de paiement</th>
                                        <th>Notes</th>
                                        <th class="text-end">Montant</th>
                                        <th class="text-end">Cumul payé</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $cumul = 0; @endphp
                                    @foreach($vente->paiements as $i => $p)
                                        @php $cumul += $p->montant; @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
                                            <td>{{ $p->mode_paiement ?? '—' }}</td>
                                            <td>{{ $p->notes ?? '—' }}</td>
                                            <td class="text-end fw-bold text-success">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end text-primary">{{ number_format($cumul, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-center">
                                                <a href="{{ route('ventes.printPaiement', [$vente->id, $p->id]) }}"
                                                   class="btn btn-sm btn-outline-secondary" target="_blank" title="Imprimer ce paiement">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total payé :</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-end fw-bold {{ $vente->reste_a_payer > 0 ? 'text-danger' : 'text-success' }}">
                                            Reste : {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($vente->statut === 'non_solde')
    <!-- Modal Solder -->
    <div class="modal fade" id="modalSolderVente" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-cash-coin"></i> Enregistrer un paiement — {{ $vente->numero_vente }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('ventes.solder', $vente->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="p-2 rounded text-center" style="background:#e3f2fd;">
                                    <small class="text-muted d-block" style="font-size:.75rem;">Total</small>
                                    <strong class="text-success small">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded text-center" style="background:#e3f2fd;">
                                    <small class="text-muted d-block" style="font-size:.75rem;">Déjà payé</small>
                                    <strong class="text-primary small">{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded text-center" style="background:#fdecea;">
                                    <small class="text-muted d-block" style="font-size:.75rem;">Reste</small>
                                    <strong class="text-danger small">{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</strong>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Montant du paiement <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg" name="montant"
                                value="{{ $vente->reste_a_payer }}" step="0.01" min="0.01" max="{{ $vente->reste_a_payer }}" required>
                            <div class="form-text">Maximum : {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date du paiement <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_paiement" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mode de paiement</label>
                                <select class="form-select" name="mode_paiement">
                                    <option value="">— Sélectionner —</option>
                                    <option>Espèces</option>
                                    <option>Chèque</option>
                                    <option>Virement bancaire</option>
                                    <option>Mobile Money</option>
                                    <option>Carte bancaire</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Observation facultative..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection
