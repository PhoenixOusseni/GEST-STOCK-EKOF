{{-- Badge Component --}}
<span class="badge badge-{{ $type ?? 'primary' }} {{ $class ?? '' }}">
    @if ($icon ?? null)
        <i class="bi bi-{{ $icon }} me-1"></i>
    @endif
    {{ $slot }}
</span>
