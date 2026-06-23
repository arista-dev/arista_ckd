@extends('layouts.app')
@section('page-title', 'Detail Model')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('master.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <h5 class="fw-bold mb-0">{{ $master->code }} — {{ $master->name }}</h5>
        <p class="text-muted mb-0" style="font-size:13px;">
            {{ $master->description ?? 'Tidak ada deskripsi' }}
        </p>
    </div>
    <a href="{{ route('master.edit', $master) }}" class="btn btn-dark btn-sm">
        <i class="bi bi-pencil me-1"></i> Edit Model
    </a>
</div>

{{-- Info Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4 text-primary">{{ $master->components->count() }}</div>
            <div class="text-muted" style="font-size:12px;">Total Komponen</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4 text-success">{{ $master->components->where('is_active', true)->count() }}</div>
            <div class="text-muted" style="font-size:12px;">Komponen Aktif</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4 text-secondary">{{ $master->receivings()->count() }}</div>
            <div class="text-muted" style="font-size:12px;">Total Receiving</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4 {{ $master->is_active ? 'text-success' : 'text-danger' }}">
                {{ $master->is_active ? 'Aktif' : 'Nonaktif' }}
            </div>
            <div class="text-muted" style="font-size:12px;">Status Model</div>
        </div>
    </div>
</div>

{{-- Component Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Komponen</h6>
        <a href="{{ route('master.edit', $master) }}" class="btn btn-sm btn-outline-dark">
            <i class="bi bi-pencil me-1"></i> Kelola Komponen
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;" class="px-4">#</th>
                    <th style="font-size:12px;">Kode</th>
                    <th style="font-size:12px;">Nama Komponen</th>
                    <th style="font-size:12px;" class="text-center">Expected Qty</th>
                    <th style="font-size:12px;" class="text-center">Status</th>
                    <th style="font-size:12px;" class="text-center">Riwayat Inspeksi</th>
                    <th style="font-size:12px;" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($master->components as $i => $comp)
                <tr class="{{ !$comp->is_active ? 'table-secondary opacity-75' : '' }}">
                    <td class="px-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $comp->code }}</span>
                    </td>
                    <td class="fw-semibold" style="font-size:13px;">{{ $comp->name }}</td>
                    <td class="text-center" style="font-size:13px;">{{ $comp->expected_qty }}</td>
                    <td class="text-center">
                        <span class="badge {{ $comp->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $comp->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-center" style="font-size:13px;">
                        {{ $comp->inspectionItems()->count() }} inspeksi
                    </td>
                    <td class="text-center">
                        <form method="POST"
                              action="{{ route('master.component.toggle', [$master, $comp]) }}">
                            @csrf
                            <button class="btn btn-sm {{ $comp->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                @if($comp->is_active)
                                    <i class="bi bi-pause-circle"></i> Nonaktifkan
                                @else
                                    <i class="bi bi-play-circle"></i> Aktifkan
                                @endif
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
