{{-- Breadcrumb Component --}}
<nav aria-label="breadcrumb" class="{{ $class ?? '' }}">
    <ol class="breadcrumb" style="margin-bottom: 0;">
        @foreach ($items as $label => $url)
            @if ($loop->last)
                <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $url }}" style="color: #1565c0; text-decoration: none; transition: all 0.3s ease;">
                        {{ $label }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>

<style>
    .breadcrumb a:hover {
        color: #0d47a1 !important;
        text-decoration: underline;
    }
</style>
