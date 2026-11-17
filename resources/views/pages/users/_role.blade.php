@php
    $roleActuel = old('role', isset($user) ? $user->role : '');
    $depotActuel = old('depot_id', isset($user) ? $user->depot_id : '');
@endphp

<div class="row mb-3">
    <label for="role" class="col-md-4 col-form-label">Rôle <span class="text-danger">*</span></label>
    <div class="col-md-8">
        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="">-- Sélectionner un rôle --</option>
            @foreach (\App\Support\Acces::ROLES as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected($roleActuel === $valeur)>{{ $libelle }}</option>
            @endforeach
        </select>
        @error('role')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row mb-3" id="champ-magazin" @if($roleActuel !== 'magazin') hidden @endif>
    <label for="depot_id" class="col-md-4 col-form-label">Magazin <span class="text-danger">*</span></label>
    <div class="col-md-8">
        <select class="form-select @error('depot_id') is-invalid @enderror" id="depot_id" name="depot_id">
            <option value="">-- Sélectionner un magazin --</option>
            @foreach ($depots as $depot)
                <option value="{{ $depot->id }}" @selected((string) $depotActuel === (string) $depot->id)>{{ $depot->designation }}</option>
            @endforeach
        </select>
        @error('depot_id')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        <small class="text-muted">Le magazinier consulte uniquement ce magazin.</small>
    </div>
</div>

<script>
    (function () {
        const role = document.getElementById('role');
        const bloc = document.getElementById('champ-magazin');
        const depot = document.getElementById('depot_id');
        if (!role || !bloc || !depot) return;

        function sync() {
            const magazin = role.value === 'magazin';
            bloc.hidden = !magazin;
            depot.required = magazin;
            if (!magazin) depot.value = '';
        }

        role.addEventListener('change', sync);
        sync();
    })();
</script>
