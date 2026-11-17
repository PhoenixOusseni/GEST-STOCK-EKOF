{{-- Alert Component --}}
<div class="alert alert-{{ $type ?? 'info' }} {{ $class ?? '' }}" role="alert">
    <div class="d-flex align-items-start gap-3">
        <i class="bi {{ $icon ?? 'bi-info-circle' }} flex-shrink-0"></i>
        <div>
            @if ($title ?? null)
                <h6 class="mb-2 fw-semibold">{{ $title }}</h6>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
