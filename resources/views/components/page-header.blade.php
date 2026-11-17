{{-- Page Header Component --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="page-title mb-2">{{ $title }}</h1>
        @if ($subtitle ?? null)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($action ?? null)
        <div class="d-flex gap-2 flex-wrap">
            {{ $action }}
        </div>
    @endif
</div>

@if (count($breadcrumbs ?? []) > 0)
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $label => $url)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $url }}" style="color: #1565c0;">{{ $label }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
