@extends('layouts.app')
@section('page-title', 'Edit Model')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('master.show', $master) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Edit Model — {{ $master->code }}</h5>
        <p class="text-muted mb-0" style="font-size:13px;">Update informasi model dan komponen</p>
    </div>
</div>

<form method="POST" action="{{ route('master.update', $master) }}" id="modelForm">
@csrf @method('PUT')

<div class="row g-4">
    {{-- Left: Model Info --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Info Model</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Kode Model <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="code"
                           class="form-control text-uppercase @error('code') is-invalid @enderror"
                           value="{{ old('code', $master->code) }}"
                           maxlength="20" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nama Model <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $master->name) }}"
                           maxlength="100" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control"
                              placeholder="Deskripsi opsional...">{{ old('description', $master->description) }}</textarea>
                </div>

                <div class="mb-1">
                    <label class="form-label fw-semibold">Status Model</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="is_active" id="is_active" value="1"
                               {{ old('is_active', $master->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Model Aktif
                        </label>
                    </div>
                    @error('is_active')
                        <div class="text-danger" style="font-size:13px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Danger zone --}}
        @if($master->receivings()->count() === 0)
        <div class="card border-danger border-opacity-50 shadow-sm mt-3">
            <div class="card-body">
                <p class="fw-semibold text-danger mb-2" style="font-size:13px;">
                    <i class="bi bi-exclamation-triangle me-1"></i> Danger Zone
                </p>
                <p class="text-muted mb-3" style="font-size:12px;">
                    Hapus model ini beserta semua komponennya. Tidak dapat dibatalkan.
                </p>
                <form method="POST" action="{{ route('master.destroy', $master) }}"
                      onsubmit="return confirm('Yakin hapus model {{ $master->code }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i> Hapus Model
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: Components --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Komponen</h6>
                <button type="button" class="btn btn-sm btn-outline-dark" id="addRow">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Komponen
                </button>
            </div>
            <div class="card-body p-0">
                @error('components')
                    <div class="alert alert-danger m-3 py-2">{{ $message }}</div>
                @enderror

                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;" class="px-3" width="120">Kode</th>
                            <th style="font-size:12px;">Nama Komponen</th>
                            <th style="font-size:12px;" width="110" class="text-center">Expected Qty</th>
                            <th style="font-size:12px;" width="80" class="text-center">Riwayat</th>
                            <th width="48"></th>
                        </tr>
                    </thead>
                    <tbody id="compBody">
                        @php
                            $oldComponents = old('components');
                            $components    = $oldComponents
                                ? collect($oldComponents)
                                : $master->components;
                        @endphp

                        @foreach($components as $idx => $comp)
                        @php
                            $isArray    = is_array($comp);
                            $compId     = $isArray ? ($comp['id'] ?? '') : $comp->id;
                            $compCode   = $isArray ? $comp['code']         : $comp->code;
                            $compName   = $isArray ? $comp['name']         : $comp->name;
                            $compQty    = $isArray ? $comp['expected_qty'] : $comp->expected_qty;
                            $hasHistory = !$isArray && $comp->inspectionItems()->exists();
                        @endphp
                        <tr class="comp-row">
                            <td class="px-3">
                                <input type="hidden" name="components[{{ $idx }}][id]" value="{{ $compId }}">
                                <input type="text" name="components[{{ $idx }}][code]"
                                       class="form-control form-control-sm text-uppercase"
                                       value="{{ $compCode }}"
                                       placeholder="BP" required maxlength="20"
                                       {{ $hasHistory ? 'readonly' : '' }}>
                                @if($hasHistory)
                                    <small class="text-muted d-block mt-1" style="font-size:10px;">
                                        <i class="bi bi-lock"></i> Ada riwayat inspeksi
                                    </small>
                                @endif
                            </td>
                            <td>
                                <input type="text" name="components[{{ $idx }}][name]"
                                       class="form-control form-control-sm"
                                       value="{{ $compName }}"
                                       placeholder="Battery Pack" required maxlength="100">
                            </td>
                            <td>
                                <input type="number" name="components[{{ $idx }}][expected_qty]"
                                       class="form-control form-control-sm text-center"
                                       value="{{ $compQty }}" min="1" required>
                            </td>
                            <td class="text-center">
                                @if($hasHistory)
                                    <span class="badge bg-info" title="Komponen ini sudah pernah diinspeksi">
                                        <i class="bi bi-clock-history"></i>
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:11px;">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$hasHistory)
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-3 border-top bg-light" style="font-size:12px; color:#6c757d;">
                    <i class="bi bi-info-circle me-1"></i>
                    Komponen dengan riwayat inspeksi tidak dapat dihapus (hanya bisa dinonaktifkan dari halaman Detail).
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-dark px-4">
        <i class="bi bi-save me-1"></i> Simpan Perubahan
    </button>
    <a href="{{ route('master.show', $master) }}" class="btn btn-outline-secondary px-4">
        Batal
    </a>
</div>

</form>
@endsection

@section('scripts')
<script>
let rowIndex = {{ $master->components->count() }};

function newRow(idx) {
    return `
    <tr class="comp-row">
        <td class="px-3">
            <input type="hidden" name="components[${idx}][id]" value="">
            <input type="text" name="components[${idx}][code]"
                   class="form-control form-control-sm text-uppercase"
                   placeholder="WHL" required maxlength="20">
        </td>
        <td>
            <input type="text" name="components[${idx}][name]"
                   class="form-control form-control-sm"
                   placeholder="Nama Komponen" required maxlength="100">
        </td>
        <td>
            <input type="number" name="components[${idx}][expected_qty]"
                   class="form-control form-control-sm text-center"
                   value="1" min="1" required>
        </td>
        <td></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;
}

document.getElementById('addRow').addEventListener('click', function () {
    document.getElementById('compBody').insertAdjacentHTML('beforeend', newRow(rowIndex++));
});

document.getElementById('compBody').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row');
    if (!btn) return;
    const rows = document.querySelectorAll('.comp-row');
    if (rows.length <= 1) {
        alert('Minimal 1 komponen harus ada.');
        return;
    }
    btn.closest('tr').remove();
});

// Auto uppercase
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('text-uppercase') && !e.target.readOnly) {
        const pos = e.target.selectionStart;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(pos, pos);
    }
});
</script>
@endsection
