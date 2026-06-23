@extends('layouts.app')
@section('page-title', 'Report')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1">Inspection Report</h5>
    <p class="text-muted mb-0" style="font-size:13px;">Laporan hasil pemeriksaan CKD Kit</p>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('report.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ $dateTo }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Model</label>
                <select name="ckd_model_id" class="form-select form-select-sm">
                    <option value="">— All Models —</option>
                    @foreach($models as $m)
                        <option value="{{ $m->id }}"
                            {{ $modelId == $m->id ? 'selected' : '' }}>
                            {{ $m->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm px-3">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('report.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-x-lg"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Export + Count --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted" style="font-size:13px;">
        Total: <strong>{{ $items->count() }}</strong> baris
    </span>
    <a href="{{ route('report.export', array_filter([
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
            'ckd_model_id' => $modelId,
        ])) }}"
       class="btn btn-success btn-sm px-3">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
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
                        <td class="px-4 text-muted" style="font-size:12px;">{{ $i + 1 }}</td>
                        <td style="font-size:12px;" class="fw-semibold">
                            {{ $item->inspection->inspection_no }}
                        </td>
                        <td style="font-size:12px;">
                            {{ $item->inspection->receiving->receiving_no }}
                        </td>
                        <td style="font-size:12px;">
                            {{ $item->inspection->receiving->ckdModel->code }}
                        </td>
                        <td style="font-size:12px;">
                            {{ $item->inspection->inspector?->name ?? '-' }}
                        </td>
                        <td style="font-size:12px;">{{ $item->component_name }}</td>
                        <td style="font-size:12px;" class="text-center">{{ $item->expected_qty }}</td>
                        <td style="font-size:12px;" class="text-center">
                            {{ $item->actual_qty ?? '-' }}
                        </td>
                        <td style="font-size:12px;" class="text-center">
                            @if($item->short_qty > 0)
                                <span class="text-warning fw-bold">{{ $item->short_qty }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
                        </td>
                        <td style="font-size:12px;" class="text-muted">
                            {{ $item->damage_remark ?: '—' }}
                        </td>
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
        </div>
    </div>
</div>
@endsection
