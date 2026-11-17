{{-- Table Component --}}
<div class="table-responsive card">
    <table class="table {{ $class ?? '' }}" id="{{ $id ?? 'dataTable' }}">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
                @if (($actions ?? null))
                    <th style="width: 150px; text-align: center;">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>

    @if (!($rows ?? null) || count($rows ?? []) === 0)
        <div style="padding: 2rem; text-align: center; color: #6b7280;">
            <i class="bi bi-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
            <p>{{ $emptyMessage ?? 'Aucune donnée disponible' }}</p>
        </div>
    @endif
</div>
