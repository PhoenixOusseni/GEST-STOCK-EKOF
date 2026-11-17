{{-- Usage: @include('partials.modal-solder-vente', ['vente' => $vente]) --}}
<div class="modal fade" id="modalSolderVente{{ $vente->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-cash-coin"></i> Paiement — {{ $vente->numero_vente }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ventes.solder', $vente->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="p-2 rounded text-center" style="background:#e3f2fd;">
                                <small class="text-muted d-block" style="font-size:.72rem;">Total</small>
                                <strong class="text-success small">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded text-center" style="background:#e3f2fd;">
                                <small class="text-muted d-block" style="font-size:.72rem;">Déjà payé</small>
                                <strong class="text-primary small">{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded text-center" style="background:#fdecea;">
                                <small class="text-muted d-block" style="font-size:.72rem;">Reste</small>
                                <strong class="text-danger small">{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant du paiement <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="montant"
                            value="{{ $vente->reste_a_payer }}" step="0.01" min="0.01" max="{{ $vente->reste_a_payer }}" required>
                        <div class="form-text">Maximum : {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_paiement" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6 mb-3">
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
                    <div class="mb-1">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Observation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
