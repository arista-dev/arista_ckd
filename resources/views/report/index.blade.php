@extends('layouts.app')
@section('page-title', 'Report')

@section('content')
    <div class="card-header d-flex justify-content-between align-items-center mb-4">
        <div class="py-3 d-flex justify-content-between align-items-center w-100">
            <div>
                <h5 class="fw-bold mb-1">Inspection Report</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    Laporan hasil pemeriksaan CKD Kit
                </p>
            </div>

            <form method="GET" action="{{ route('report.index') }}" id="searchForm">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                <input type="hidden" name="ckd_model_id" value="{{ $modelId }}">

                <div class="input-group input-group-sm" style="width:250px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" id="searchComponent" class="form-control border-start-0 ps-0"
                        placeholder="Inspection / Receiving / Component..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    {{-- Export Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted" style="font-size:13px;">
            Total: <strong>{{ $items->total() }}</strong> baris
        </span>
        <a href="{{ route(
            'report.export',
            array_filter([
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'ckd_model_id' => $modelId,
                'search' => request('search'),
            ]),
        ) }}"
            class="btn btn-success btn-sm px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>
            Export CSV
        </a>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;" class="px-4">#</th>
                            <th style="font-size:12px;">Inspection No</th>
                            <th style="font-size:12px;">Receiving No</th>
                            <th style="font-size:12px;">Model</th>
                            <th style="font-size:12px;">Inspector</th>
                            <th style="font-size:12px;">Component</th>
                            <th style="font-size:12px;" class="text-center">Expected</th>
                            <th style="font-size:12px;" class="text-center">Actual</th>
                            <th style="font-size:12px;" class="text-center">Short</th>
                            <th style="font-size:12px;">Status</th>
                            <th style="font-size:12px;">Damage Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item->inspection->inspection_no }}</td>
                                <td>{{ $item->inspection->receiving->receiving_no }}</td>
                                <td>{{ $item->inspection->receiving->ckdModel->code }}</td>
                                <td>{{ $item->inspection->inspector->name ?? '-' }}</td>
                                <td>{{ $item->component_name }}</td>
                                <td class="text-center">{{ $item->expected_qty }}</td>
                                <td class="text-center">{{ $item->actual_qty }}</td>
                                <td class="text-center">{{ $item->short_qty }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->damage_remark ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-bar-graph fs-3 d-block mb-2 opacity-25"></i>
                                    Tidak ada data untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 px-3">
                    <small class="text-muted">
                        Showing {{ $items->firstItem() ?? 0 }}
                        -
                        {{ $items->lastItem() ?? 0 }}
                        of {{ $items->total() }} records
                    </small>

                    {{ $items->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        let searchTimeout;

        document.getElementById('searchComponent').addEventListener('input', function() {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 1500);
        });
    </script>
@endsection
