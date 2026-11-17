{{-- Card Component --}}
<div class="card {{ $class ?? '' }}">
    @if ($title ?? null)
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $title }}</h5>
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if ($footer ?? null)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
