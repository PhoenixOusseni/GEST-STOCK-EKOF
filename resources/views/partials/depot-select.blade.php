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
        <small class="text-muted">Choisissez le magazin avant les articles. Les quantités affichées sont celles de ce magazin.</small>
        @endif
        @error('depot_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
