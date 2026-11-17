{{-- Action Buttons Group Component --}}
<div class="btn-group btn-group-sm" role="group" {{ $attributes->merge([]) }}>
    @if ($editUrl ?? null)
        <a href="{{ $editUrl }}" class="btn btn-outline-primary btn-sm" title="Éditer">
            <i class="bi bi-pencil"></i>
        </a>
    @endif

    @if ($viewUrl ?? null)
        <a href="{{ $viewUrl }}" class="btn btn-outline-info btn-sm" title="Voir">
            <i class="bi bi-eye"></i>
        </a>
    @endif

    @if ($deleteUrl ?? null)
        <form action="{{ $deleteUrl }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer" data-confirm="delete">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif

    {{ $slot }}
</div>
