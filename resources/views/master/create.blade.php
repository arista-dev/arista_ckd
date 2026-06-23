@extends('layouts.app')
@section('page-title', 'Tambah Model')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('master.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Tambah Model CKD</h5>
        <p class="text-muted mb-0" style="font-size:13px;">Buat model kendaraan baru beserta komponennya</p>
    </div>
</div>

<form method="POST" action="{{ route('master.store') }}" id="modelForm">
@csrf

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
                           value="{{ old('code') }}"
                           placeholder="Contoh: EV-X3"
                           maxlength="20" required>
                    <div class="form-text">Huruf kapital otomatis. Maks. 20 karakter.</div>
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
                           value="{{ old('name') }}"
                           placeholder="Contoh: Electric Vehicle X3"
                           maxlength="100" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Deskripsi opsional...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
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

                <table class="table align-middle mb-0" id="compTable">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;" class="px-3" width="120">Kode</th>
                            <th style="font-size:12px;">Nama Komponen</th>
                            <th style="font-size:12px;" width="110" class="text-center">Expected Qty</th>
                            <th width="48"></th>
                        </tr>
                    </thead>
                    <tbody id="compBody">
                        @if(old('components'))
                            @foreach(old('components') as $idx => $comp)
                            <tr class="comp-row">
                                <td class="px-3">
                                    <input type="text" name="components[{{ $idx }}][code]"
                                           class="form-control form-control-sm text-uppercase"
                                           value="{{ $comp['code'] }}" placeholder="BP" required maxlength="20">
                                </td>
                                <td>
                                    <input type="text" name="components[{{ $idx }}][name]"
                                           class="form-control form-control-sm"
                                           value="{{ $comp['name'] }}" placeholder="Battery Pack" required maxlength="100">
                                </td>
                                <td>
                                    <input type="number" name="components[{{ $idx }}][expected_qty]"
                                           class="form-control form-control-sm text-center"
                                           value="{{ $comp['expected_qty'] }}" min="1" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            {{-- Default 1 empty row --}}
                            <tr class="comp-row">
                                <td class="px-3">
                                    <input type="text" name="components[0][code]"
                                           class="form-control form-control-sm text-uppercase"
                                           placeholder="BP" required maxlength="20">
                                </td>
                                <td>
                                    <input type="text" name="components[0][name]"
                                           class="form-control form-control-sm"
                                           placeholder="Battery Pack" required maxlength="100">
                                </td>
                                <td>
                                    <input type="number" name="components[0][expected_qty]"
                                           class="form-control form-control-sm text-center"
                                           value="1" min="1" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="p-3 border-top bg-light" style="font-size:12px; color:#6c757d;">
                    <i class="bi bi-info-circle me-1"></i>
                    Kode komponen akan otomatis huruf kapital. Minimal 1 komponen wajib diisi.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-dark px-4">
        <i class="bi bi-save me-1"></i> Simpan Model
    </button>
    <a href="{{ route('master.index') }}" class="btn btn-outline-secondary px-4">
        Batal
    </a>
</div>

</form>
@endsection

@section('scripts')
<script>
let rowIndex = {{ old('components') ? count(old('components')) : 1 }};

function newRow(idx) {
    return `
    <tr class="comp-row">
        <td class="px-3">
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

// Auto uppercase code inputs
document.getElementById('compBody').addEventListener('input', function (e) {
    if (e.target.classList.contains('text-uppercase')) {
        const pos = e.target.selectionStart;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(pos, pos);
    }
});
document.querySelector('input[name="code"]').addEventListener('input', function () {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});
</script>
@endsection
