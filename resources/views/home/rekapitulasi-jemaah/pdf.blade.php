<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] }}</title>
    <style>
        @page { margin: 22px 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #28231d; font-size: 9px; }
        h1 { margin: 0 0 4px; color: #7a4b13; font-size: 19px; }
        .brand { font-weight: bold; font-size: 11px; margin-bottom: 14px; }
        .meta { width: 100%; margin-bottom: 12px; }
        .meta td { border: 0; padding: 2px 8px 2px 0; }
        .meta .label { width: 80px; color: #777; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #ddd4c7; padding: 5px 4px; }
        table.data th { background: #8a5b19; color: #fff; text-align: center; font-size: 8px; }
        table.data td:not(:first-child):not(:nth-child(2)) { text-align: right; }
        table.data tbody tr:nth-child(even) { background: #fbf8f2; }
        table.data tfoot td { background: #fff1d8; font-weight: bold; }
        .note { color: #6c6358; font-size: 8px; margin-top: 9px; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; color: #8d857b; font-size: 7px; text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $report['title'] }} Jemaah</h1>
    <div class="brand">Sawdeera Tour &amp; Travel</div>
    <table class="meta">
        <tr><td class="label">Periode</td><td>: {{ $report['period_label'] }}</td><td class="label">Paket</td><td>: {{ $report['package_label'] }}</td></tr>
    </table>
    <table class="data">
        <thead>
            <tr>
                @foreach($report['columns'] as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($report['rows'] as $row)
                <tr>
                    @foreach($report['columns'] as $column)
                        <td>{{ $recapService->formatValue($row[$column['key']] ?? 0, $column['format']) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                @foreach($report['columns'] as $column)
                    <td>
                        @if($column['key'] === 'package_name')
                            TOTAL
                        @elseif($column['key'] !== 'month_label')
                            {{ $recapService->formatValue($report['totals'][$column['key']] ?? 0, $column['format']) }}
                        @endif
                    </td>
                @endforeach
            </tr>
        </tfoot>
    </table>
    <div class="note">* {{ $report['note'] }}</div>
    <div class="footer">Dicetak {{ $report['generated_at'] }}</div>
</body>
</html>
