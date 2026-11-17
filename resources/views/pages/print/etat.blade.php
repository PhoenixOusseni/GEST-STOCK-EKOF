<!DOCTYPE html>
<html lang="fr">
<head>
    @include('partials.invoice-style')
    <title>{{ $docLabel }}</title>
</head>
<body>
    <div class="inv-toolbar wide no-print">
        <button class="inv-btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
        <a href="{{ $backUrl }}" class="inv-btn-back">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="inv-page inv-page-wide">
        @include('partials.invoice-header', [
            'docLabel' => $docLabel,
            'docNumber' => $docNumber,
            'docDate' => $docDate,
        ])

        @if (!empty($meta))
            <div class="inv-meta">
                @foreach ($meta as $item)
                    <div class="inv-meta-item">
                        <div class="inv-meta-label">{{ $item[0] }}</div>
                        <div class="inv-meta-value">{{ $item[1] }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="inv-body">
            <table class="inv-table">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th class="{{ $column['class'] ?? '' }}" @if(!empty($column['width'])) style="width: {{ $column['width'] }}" @endif>
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            @foreach ($row as $index => $cell)
                                <td class="{{ $columns[$index]['class'] ?? '' }}">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="inv-center inv-muted-cell">Aucune ligne à imprimer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (!empty($totals))
                <div class="inv-totals-wrap">
                    <div class="inv-totals">
                        @foreach ($totals as $total)
                            @if ($loop->last)
                                <div class="inv-totals-grand">
                                    <span class="lbl">{{ $total[0] }}</span>
                                    <span class="val">{{ $total[1] }}</span>
                                </div>
                            @else
                                <div class="inv-totals-row {{ $total[2] ?? '' }}">
                                    <span>{{ $total[0] }}</span>
                                    <span>{{ $total[1] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @include('partials.invoice-footer', ['footerMessage' => $footerMessage ?? 'Document interne. À conserver.'])
    </div>
</body>
</html>
