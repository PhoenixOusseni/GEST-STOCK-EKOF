{{-- Stats Card Component --}}
<div class="card stat-card h-100 {{ $class ?? '' }}">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <p class="text-muted mb-1" style="font-size: 0.9rem;">{{ $label }}</p>
                <h3 class="mb-0 fw-bold" style="color: {{ $color ?? '#1565c0' }};">
                    {{ $value }}
                </h3>
                @if ($subtitle ?? null)
                    <small class="text-muted d-block mt-2">{{ $subtitle }}</small>
                @endif
            </div>
            @if ($icon ?? null)
                <div style="width: 50px; height: 50px; border-radius: 5px; background: rgba({{ $colorRgb ?? '46, 125, 50' }}, 0.1); display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-{{ $icon }}" style="font-size: 1.5rem; color: {{ $color ?? '#1565c0' }};"></i>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-left: 4px solid {{ $color ?? '#1565c0' }};
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>
