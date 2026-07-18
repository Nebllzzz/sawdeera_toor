@extends('layouts.main')

@section('title', 'Rekapitulasi Data Jemaah')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
@endsection

@section('content')
    <div class="content-wrapper recap-page">
        <x-page-heading
            title="Rekapitulasi Data Jemaah"
            description="Ringkasan bulanan data jemaah berdasarkan paket dan periode yang dipilih."
            section="Laporan"
            current="Rekapitulasi Pendaftaran"
            breadcrumb-id="breadcrumbReport"
        >
            <x-slot:actions>
                <button type="button" class="btn-export excel" id="btnExportExcel"><i class="fas fa-file-excel"></i> Export Excel</button>
                <button type="button" class="btn-export pdf" id="btnExportPdf"><i class="fas fa-file-pdf"></i> Cetak PDF</button>
            </x-slot:actions>
        </x-page-heading>

        <div class="card recap-filter-card">
            <div class="card-body">
                <div class="recap-filter-grid">
                    <div class="filter-field report-field">
                        <label for="filterType">Jenis Laporan</label>
                        <select id="filterType" class="form-select">
                            @foreach ($reportTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="filterPackage">Paket Umrah</label>
                        <select id="filterPackage" class="form-select">
                            <option value="all">Semua Paket Umrah</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->nama_paket }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="filterPeriod">Periode</label>
                        <div class="period-input-wrap">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" id="filterPeriod" class="form-control" autocomplete="off"
                                aria-label="Periode laporan">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="filter-buttons">
                        <button type="button" id="btnApply" class="btn-apply"><i class="fas fa-filter"></i>
                            Terapkan</button>
                        <button type="button" id="btnReset" class="btn-reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="reportLoading" class="recap-loading">
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Menyiapkan rekapitulasi...
        </div>

        <div id="reportError" class="alert alert-danger d-none" role="alert"></div>

        <div id="reportContent" class="d-none">
            <div id="summaryCards" class="recap-summary"></div>

            <div class="card recap-table-card">
                <div class="card-header">
                    <div>
                        <h2 id="tableTitle">Ringkasan Pendaftaran</h2>
                        <p id="tableSubtitle"></p>
                    </div>
                    <span id="rowCount" class="row-count"></span>
                </div>
                <div class="card-body pt-0">
                    <div class="recap-table-wrap">
                        <table class="table recap-table" id="recapTable">
                            <thead></thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="table-note"><i class="fas fa-circle-info"></i><span id="tableNote"></span></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .recap-page {
            padding: 4px 12px 24px
        }

        .btn-export {
            height: 40px;
            border-radius: 9px;
            padding: 0 15px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #e1ddd6;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: .15s
        }

        .btn-export.excel {
            color: #178547
        }

        .btn-export.pdf {
            color: #c83c36
        }

        .btn-export:hover {
            box-shadow: 0 5px 14px rgba(57, 40, 18, .1);
            transform: translateY(-1px)
        }

        .btn-export:disabled {
            opacity: .55;
            transform: none;
            cursor: not-allowed
        }

        .recap-filter-card {
            border-radius: 12px;
            margin-bottom: 18px
        }

        .recap-filter-card .card-body {
            padding: 18px
        }

        .recap-filter-grid {
            display: grid;
            grid-template-columns: minmax(220px, 1.25fr) minmax(185px, 1fr) minmax(245px, 1.25fr) auto;
            gap: 14px;
            align-items: end
        }

        .filter-field label {
            display: block;
            color: #5f584f;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 7px
        }

        .filter-field .form-select,
        .filter-field .form-control {
            height: 42px;
            border: 1px solid #ded9d1;
            border-radius: 8px;
            font-size: 12px;
            color: #3a342d;
            background-color: #fff
        }

        .period-input-wrap {
            position: relative
        }

        .period-input-wrap .form-control {
            padding-left: 36px;
            padding-right: 34px
        }

        .period-input-wrap>i:first-child {
            position: absolute;
            left: 13px;
            top: 14px;
            color: #a06b1d;
            z-index: 2
        }

        .period-input-wrap>i:last-child {
            position: absolute;
            right: 13px;
            top: 16px;
            color: #968e84;
            font-size: 10px;
            pointer-events: none
        }

        .filter-buttons {
            display: flex;
            gap: 8px
        }

        .btn-apply,
        .btn-reset {
            height: 42px;
            border-radius: 8px;
            padding: 0 16px;
            border: 0;
            font-weight: 700;
            font-size: 12px
        }

        .btn-apply {
            background: linear-gradient(135deg, #a96e18, #7d4c0c);
            color: #fff;
            box-shadow: 0 6px 13px rgba(139, 89, 18, .2)
        }

        .btn-reset {
            background: #fff;
            color: #766f66;
            border: 1px solid #ded9d1
        }

        .btn-apply:disabled {
            opacity: .6
        }

        .recap-loading {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #8a5b19;
            font-size: 13px
        }

        .recap-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
            gap: 12px;
            margin-bottom: 18px
        }

        .summary-card {
            background: #fff;
            border: 1px solid #e8e3dc;
            border-radius: 11px;
            padding: 16px;
            min-height: 105px;
            display: flex;
            align-items: center;
            gap: 13px;
            box-shadow: 0 4px 15px rgba(44, 31, 15, .04)
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px
        }

        .summary-card.blue .summary-icon {
            background: #e9f3ff;
            color: #3479c7
        }

        .summary-card.green .summary-icon {
            background: #eaf8ef;
            color: #2e9b58
        }

        .summary-card.amber .summary-icon {
            background: #fff5df;
            color: #c2801e
        }

        .summary-card.red .summary-icon {
            background: #fff0ed;
            color: #d95749
        }

        .summary-card.purple .summary-icon {
            background: #f2edff;
            color: #7d5ac7
        }

        .summary-card.gray .summary-icon {
            background: #f0f1f2;
            color: #6d747c
        }

        .summary-card.cyan .summary-icon {
            background: #e8f8fa;
            color: #3098a7
        }

        .summary-copy {
            min-width: 0
        }

        .summary-label {
            font-size: 10px;
            color: #79736b;
            font-weight: 700;
            margin-bottom: 3px
        }

        .summary-value {
            font-size: 21px;
            line-height: 1.15;
            color: #28221c;
            font-weight: 800;
            overflow-wrap: anywhere
        }

        .summary-detail {
            font-size: 9px;
            color: #8d857c;
            margin-top: 4px
        }

        .recap-table-card .card-header {
            padding: 18px 20px;
            border-bottom: 0
        }

        .recap-table-card .card-header h2 {
            font-size: 15px;
            font-weight: 800
        }

        .recap-table-card .card-header p {
            font-size: 10px;
            color: #8a837a;
            margin: 4px 0 0
        }

        .row-count {
            border-radius: 20px;
            background: #fff5df;
            color: #8a5b19;
            font-size: 10px;
            font-weight: 700;
            padding: 6px 10px
        }

        .recap-table-card .card-body {
            padding-left: 20px;
            padding-right: 20px
        }

        .recap-table-wrap {
            overflow-x: auto !important;
            border: 1px solid #e8e3dc;
            border-radius: 9px
        }

        .recap-table {
            table-layout: auto !important;
            width: 100% !important;
            margin: 0;
            min-width: 920px
        }

        .recap-table th,
        .recap-table td {
            white-space: nowrap !important;
            word-break: normal !important;
            padding: 11px 12px !important;
            border-bottom: 1px solid #eee9e2 !important;
            font-size: 10px;
            vertical-align: middle
        }

        .recap-table thead th {
            background: #faf7f2;
            color: #625a50;
            font-weight: 800;
            border-bottom: 1px solid #ded7cd !important
        }

        .recap-table thead th:not(:first-child):not(:nth-child(2)),
        .recap-table td.metric {
            text-align: right
        }

        .recap-table tbody tr:hover {
            background: #fffcf7
        }

        .recap-table .package-name {
            font-weight: 700;
            color: #3f3327;
            min-width: 200px
        }

        .recap-table .month-name {
            color: #70685f;
            min-width: 115px
        }

        .recap-table tfoot td {
            background: #fff8e9;
            color: #3b3024;
            font-weight: 800;
            border-bottom: 0 !important
        }

        .table-note {
            display: flex;
            gap: 7px;
            align-items: flex-start;
            color: #847c72;
            font-size: 9px;
            margin-top: 10px
        }

        .table-note i {
            color: #a46c1e;
            margin-top: 1px
        }

        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background-color: #93601a
        }

        .daterangepicker .btn-primary {
            background: #93601a;
            border-color: #93601a
        }

        @media(max-width:1100px) {
            .recap-filter-grid {
                grid-template-columns: 1fr 1fr
            }

            .filter-buttons {
                justify-content: flex-end
            }

        }

        @media(max-width:700px) {
            .recap-page {
                padding: 2px 2px 20px
            }

            .recap-heading-actions .btn-export {
                flex: 1;
                justify-content: center
            }

            .recap-filter-grid {
                grid-template-columns: 1fr
            }

            .filter-buttons {
                justify-content: stretch
            }

            .filter-buttons button {
                flex: 1
            }

            .recap-summary {
                grid-template-columns: 1fr 1fr
            }

            .summary-card {
                padding: 12px;
                gap: 9px
            }

            .summary-icon {
                width: 38px;
                height: 38px;
                min-width: 38px
            }

            .summary-value {
                font-size: 17px
            }
        }

        @media(max-width:420px) {
            .recap-summary {
                grid-template-columns: 1fr
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function() {
            const defaults = {
                start: @json($defaultStart),
                end: @json($defaultEnd)
            };
            let appliedFilters = null;
            let currentReport = null;

            $('#filterPeriod').daterangepicker({
                startDate: moment(defaults.start, 'YYYY-MM-DD'),
                endDate: moment(defaults.end, 'YYYY-MM-DD'),
                autoApply: false,
                opens: 'left',
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Pilih Periode',
                    cancelLabel: 'Batal',
                    fromLabel: 'Mulai',
                    toLabel: 'Selesai',
                    customRangeLabel: 'Rentang Lain',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                        'September', 'Oktober', 'November', 'Desember'
                    ],
                    firstDay: 1
                }
            });

            $('#filterType').on('change', loadReport);
            $('#btnApply').on('click', loadReport);
            $('#btnReset').on('click', function() {
                $('#filterType').val('pendaftaran');
                $('#filterPackage').val('all');
                const picker = $('#filterPeriod').data('daterangepicker');
                picker.setStartDate(moment(defaults.start, 'YYYY-MM-DD'));
                picker.setEndDate(moment(defaults.end, 'YYYY-MM-DD'));
                loadReport();
            });
            $('#btnExportExcel').on('click', function() {
                if (appliedFilters) window.location.href = exportUrl('excel');
            });
            $('#btnExportPdf').on('click', function() {
                if (appliedFilters) window.location.href = exportUrl('pdf');
            });

            function filters() {
                const picker = $('#filterPeriod').data('daterangepicker');
                return {
                    type: $('#filterType').val(),
                    package_id: $('#filterPackage').val(),
                    start_date: picker.startDate.format('YYYY-MM-DD'),
                    end_date: picker.endDate.format('YYYY-MM-DD')
                };
            }

            async function loadReport() {
                const selected = filters();
                setLoading(true);
                $('#reportError').addClass('d-none').text('');
                try {
                    const response = await fetch('/admin/rekapitulasi-jemaah/data?' + new URLSearchParams(
                        selected), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        const message = data.message || Object.values(data.errors || {}).flat()[0] ||
                            'Data rekapitulasi gagal dimuat.';
                        throw new Error(message);
                    }
                    appliedFilters = selected;
                    currentReport = data;
                    renderReport(data);
                } catch (error) {
                    $('#reportContent').addClass('d-none');
                    $('#reportError').removeClass('d-none').text(error.message);
                } finally {
                    setLoading(false);
                }
            }

            function renderReport(report) {
                $('#breadcrumbReport').text(report.title);
                $('#tableTitle').text('Ringkasan ' + report.title.replace('Rekapitulasi ', ''));
                $('#tableSubtitle').text(report.package_label + ' · ' + report.period_label);
                $('#tableNote').text(report.note);
                $('#rowCount').text(report.rows.length.toLocaleString('id-ID') + ' baris');

                $('#summaryCards').html(report.summary.map(card => `
            <div class="summary-card ${escapeHtml(card.color)}">
                <span class="summary-icon"><i class="fas ${escapeHtml(card.icon)}"></i></span>
                <div class="summary-copy">
                    <div class="summary-label">${escapeHtml(card.label)}</div>
                    <div class="summary-value">${escapeHtml(card.formatted)}</div>
                    ${card.detail ? `<div class="summary-detail">${escapeHtml(card.detail)}</div>` : ''}
                </div>
            </div>
        `).join(''));

                $('#recapTable thead').html(
                    `<tr>${report.columns.map(column => `<th>${escapeHtml(column.label)}</th>`).join('')}</tr>`);
                $('#recapTable tbody').html(report.rows.map(row => `<tr>${report.columns.map((column, index) => {
            const className = index === 0 ? 'package-name' : (index === 1 ? 'month-name' : 'metric');
            return `<td class="${className}">${escapeHtml(formatValue(row[column.key], column.format))}</td>`;
        }).join('')}</tr>`).join(''));
                $('#recapTable tfoot').html(`<tr>${report.columns.map((column, index) => {
            const value = column.key === 'package_name' ? 'TOTAL' : (column.key === 'month_label' ? '' : formatValue(report.totals[column.key] || 0, column.format));
            return `<td class="${index > 1 ? 'metric' : ''}">${escapeHtml(value)}</td>`;
        }).join('')}</tr>`);
                $('#reportContent').removeClass('d-none');
            }

            function formatValue(value, format) {
                if (format === 'text') return value == null ? '-' : String(value);
                const number = Number(value || 0);
                if (format === 'currency') return 'Rp ' + number.toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });
                if (format === 'percent') return number.toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                }) + '%';
                return number.toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });
            }

            function exportUrl(format) {
                return '/admin/rekapitulasi-jemaah/export/' + format + '?' + new URLSearchParams(appliedFilters)
                    .toString();
            }

            function setLoading(loading) {
                $('#reportLoading').toggleClass('d-none', !loading);
                $('#btnApply, #btnReset, #btnExportExcel, #btnExportPdf').prop('disabled', loading);
            }

            function escapeHtml(value) {
                return String(value == null ? '' : value).replace(/[&<>'"]/g, character => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#039;',
                    '"': '&quot;'
                })[character]);
            }

            loadReport();
        });
    </script>
@endpush
