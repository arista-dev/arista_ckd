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


    <div class="row g-4">
        {{-- Left: Model Info --}}
        <div class="col-md-4">
            <form method="POST" action="{{ route('master.update', $master) }}" id="modelForm">
                @csrf @method('PUT')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Info Model</h6>
                    </div>
                    <div class="card-body">
                        {{-- code field --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Model <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('name', $master->code) }}" maxlength="100" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- name field (your snippet) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Model <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $master->name) }}" maxlength="100" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Deskripsi opsional...">{{ old('description', $master->description) }}</textarea>
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-semibold">Status Model</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active"
                                    id="is_active" value="1" disabled
                                    {{ old('is_active', $master->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Model Aktif</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger" style="font-size:13px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit button stays INSIDE the form, right after the card --}}
                {{-- <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('master.show', $master) }}" class="btn btn-outline-secondary px-4">
                        Batal
                    </a>
                </div> --}}

            </form>
            {{-- Danger zone --}}
            {{-- @if ($master->receivings()->count() === 0)
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
            @endif --}}
        </div>

        {{-- Right: Components --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Komponen</h6>
                    <div class="d-flex gap-2 align-items-center">
                        <form method="GET" action="{{ route('master.edit', $master) }}" id="searchForm">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" id="searchComponent"
                                    class="form-control border-start-0 ps-0" placeholder="Cari Kode atau Nama..."
                                    value="{{ request('search') }}">
                            </div>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="addRowBtn">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Komponen
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:12px;" class="px-3" width="120">Kode</th>
                                <th style="font-size:12px;">Nama Komponen</th>
                                <th style="font-size:12px;" width="110" class="text-center">Expected Qty</th>
                                <th style="font-size:12px;" width="80" class="text-center">Riwayat</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="compBody">
                            @foreach ($components as $comp)
                                @php $hasHistory = $comp->inspectionItems()->exists(); @endphp
                                <tr class="comp-row" data-id="{{ $comp->id }}">
                                    <td class="align-middle px-3" style="width: 15%;">
                                        <input type="text" class="form-control form-control-sm text-uppercase field-code"
                                            value="{{ $comp->code }}" maxlength="20"
                                            {{ $hasHistory ? 'readonly' : '' }}>
                                        @if ($hasHistory)
                                            <small class="text-muted d-block mt-1" style="font-size:10px;">
                                                <i class="bi bi-lock"></i> Ada riwayat inspeksi
                                            </small>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm field-name"
                                            value="{{ $comp->name }}" maxlength="100">
                                        @if ($comp->description)
                                            <small class="text-muted d-block mt-1" style="font-size:10px;">
                                                <i class="bi bi-info-circle"></i> {{ $comp->description }}
                                            </small>
                                        @endif
                                    </td>

                                    <td class="align-middle text-center" style="width: 12%;">
                                        <input type="number"
                                            class="form-control form-control-sm text-center field-qty mx-auto"
                                            value="{{ $comp->expected_qty }}" min="1" style="max-width: 80px;">
                                    </td>

                                    <td class="align-middle text-center" style="width: 10%;">
                                        @if ($hasHistory)
                                            <span class="badge bg-info"><i class="bi bi-clock-history"></i></span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">—</span>
                                        @endif
                                    </td>

                                    <td class="align-middle text-center" style="width: 15%;">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-success save-row"
                                                title="Simpan">
                                                <i class="bi bi-check-lg save-icon"></i>
                                            </button>
                                            @if (!$hasHistory)
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="card-footer bg-white d-flex justify-content-center py-3">
                        {{ $components->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <script>
        const masterId = {{ $master->id }};
        const csrf = '{{ csrf_token() }}';

        function rowPayload(row) {
            return {
                code: row.querySelector('.field-code').value,
                name: row.querySelector('.field-name').value,
                expected_qty: row.querySelector('.field-qty').value,
            };
        }

        function flashRow(row, ok) {
            row.classList.add(ok ? 'table-success' : 'table-danger');
            setTimeout(() => row.classList.remove('table-success', 'table-danger'), 800);
        }

        // Debounced search
        let searchTimer;
        document.getElementById('searchComponent').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => document.getElementById('searchForm').submit(), 400);
        });

        // Save existing row
        document.getElementById('compBody').addEventListener('click', async function(e) {
            const saveBtn = e.target.closest('.save-row');
            const removeBtn = e.target.closest('.remove-row');
            if (!saveBtn && !removeBtn) return;

            const row = e.target.closest('tr');
            const id = row.dataset.id;

            if (saveBtn) {
                const icon = saveBtn.querySelector('.save-icon');
                const originalClass = icon.className; // remember original icon classes

                // switch to loading state
                icon.className = 'spinner-border spinner-border-sm';
                saveBtn.disabled = true;
                try {
                    const res = await fetch(`/master/${masterId}/components/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(rowPayload(row)),
                    });
                    const data = await res.json();
                    flashRow(row, res.ok && data.success);
                    if (!res.ok) alert(data.message || 'Gagal menyimpan komponen.');
                } catch (err) {
                    flashRow(row, false);
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    // always restore icon + re-enable button, success or failure
                    icon.className = originalClass;
                    saveBtn.disabled = false;
                }
            }

            if (removeBtn) {
                if (!confirm('Hapus komponen ini?')) return;
                try {
                    const res = await fetch(`/master/${masterId}/components/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                    });
                    const data = await res.json();
                    if (data.success) {
                        row.remove();
                    } else {
                        alert(data.message || 'Gagal menghapus komponen.');
                    }
                } catch (err) {
                    alert('Terjadi kesalahan jaringan.');
                }
            }
        });

        // Add new row (saved immediately via store endpoint, not deferred)
        document.getElementById('addRowBtn').addEventListener('click', async function() {
            const code = prompt('Kode komponen baru:');
            if (!code) return;
            const name = prompt('Nama komponen:');
            if (!name) return;
            const qty = prompt('Expected Qty:', '1');

            try {
                const res = await fetch(`/master/${masterId}/components`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code,
                        name,
                        expected_qty: qty || 1
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    location.reload(); // simplest way to reflect new row + correct pagination count
                } else {
                    alert(JSON.stringify(data.errors || data.message));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        });

        // Auto uppercase
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('text-uppercase') && !e.target.readOnly) {
                const pos = e.target.selectionStart;
                e.target.value = e.target.value.toUpperCase();
                e.target.setSelectionRange(pos, pos);
            }
        });
    </script>
@endsection
