{{-- Button Component --}}
<button
    type="{{ $type ?? 'button' }}"
    class="btn btn-{{ $variant ?? 'primary' }} {{ $size ? 'btn-' . $size : '' }} {{ $class ?? '' }}"
    @if ($disabled ?? false) disabled @endif
    @if ($dataLoading ?? null) data-loading-text="{{ $dataLoading }}" @endif
    {!! $attributes->merge([]) !!}
>
    @if ($icon ?? null)
        <i class="bi bi-{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</button>
