@extends('layouts.app')
@section('page-title', 'Inspection Detail')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('inspection.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0">{{ $inspection->inspection_no }}</h5>
            <p class="text-muted mb-0" style="font-size:13px;">
                Receiving: {{ $inspection->receiving->receiving_no }}
                &nbsp;|&nbsp;
                Model: {{ $inspection->receiving->ckdModel->code }}
                &nbsp;|&nbsp;
                Status: <span class="badge badge-{{ $inspection->status }}">{{ $inspection->status }}</span>
            </p>
        </div>
    </div>

    {{-- Rejection notice --}}
    @if ($inspection->status === 'OPEN' && $inspection->rejected_at)
        <div class="alert alert-danger mb-4">
            <i class="bi bi-x-circle me-2"></i>
            <strong>Inspection di-Reject</strong> oleh {{ $inspection->rejecter?->name }}
            pada {{ $inspection->rejected_at->format('d M Y H:i') }}.
            @if ($inspection->rejection_reason)
                <br><span class="ms-4">Alasan: {{ $inspection->rejection_reason }}</span>
            @endif
        </div>
    @endif



    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Component Checklist</h6>
            <form method="GET" action="{{ route('inspection.show', $inspection->id) }}" id="searchForm">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchComponent" class="form-control border-start-0 ps-0"
                        placeholder="Cari Kode atau Nama..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
        <form method="POST" action="{{ route('inspection.update', $inspection->id) }}" id="inspectionForm">
            @csrf
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:12px;" class="text-center">Component</th>
                                <th style="font-size:12px;" class="text-center">Expected Qty</th>
                                <th style="font-size:12px;" class="text-center">Actual Qty</th>
                                <th style="font-size:12px;" class="text-center">Short Qty</th>
                                <th style="font-size:12px;" class="text-center">Status</th>
                                <th style="font-size:12px;">Damage Info</th>
                                @if (!$inspection->isClosed())
                                    <th style="font-size:12px;" class="text-center" width="70">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="item-row" data-id="{{ $item->id }}">
                                    <td class="px-4 fw-semibold"
                                        style="font-size:13px; white-space: normal; word-break: break-word; overflow-wrap: anywhere; max-width: 220px;">
                                        {{ $item->component_name }}
                                        <small class="text-muted d-block" style="font-size:11px;">
                                            {{ $item->component_code }}
                                        </small>
                                    </td>

                                    <td class="text-center" style="font-size:13px;">{{ $item->expected_qty }}</td>

                                    <td class="text-center">
                                        @if ($inspection->isClosed())
                                            <span style="font-size:13px;">{{ $item->actual_qty ?? '-' }}</span>
                                        @else
                                            <input type="number"
                                                class="form-control form-control-sm text-center field-actual-qty"
                                                style="width:80px; margin:auto;"
                                                value="{{ $item->actual_qty ?? $item->expected_qty }}" min="0"
                                                data-expected="{{ $item->expected_qty }}">
                                        @endif
                                    </td>

                                    <td class="text-center field-short-qty" style="font-size:13px;">
                                        <span class="{{ $item->short_qty > 0 ? 'text-warning fw-bold' : 'text-muted' }}">
                                            {{ $item->short_qty }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if ($inspection->isClosed())
                                            <span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
                                        @else
                                            <select class="form-select form-select-sm field-status"
                                                style="width:120px; margin:auto;">
                                                <option value="OK" {{ $item->status === 'OK' ? 'selected' : '' }}>OK
                                                </option>
                                                <option value="SHORT" {{ $item->status === 'SHORT' ? 'selected' : '' }}>
                                                    SHORT</option>
                                                <option value="DAMAGE" {{ $item->status === 'DAMAGE' ? 'selected' : '' }}>
                                                    DAMAGE</option>
                                            </select>
                                        @endif
                                    </td>

                                    <td style="min-width:220px;">
                                        @if ($inspection->isClosed())
                                            @if ($item->isDamage())
                                                <small class="text-muted d-block">{{ $item->damage_remark ?? '-' }}</small>
                                                @if ($item->hasDamagePhoto())
                                                    <a href="{{ $item->damagePhotoUrl() }}" target="_blank"
                                                        class="btn btn-sm btn-outline-secondary mt-1">
                                                        <i class="bi bi-image"></i> Lihat Foto
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted" style="font-size:12px;">—</span>
                                            @endif
                                        @else
                                            <div class="field-damage-wrap"
                                                style="{{ $item->isDamage() ? '' : 'display:none;' }}">
                                                <input type="text"
                                                    class="form-control form-control-sm mb-1 field-damage-remark"
                                                    placeholder="Damage remark..."
                                                    value="{{ $item->damage_remark ?? '' }}">
                                                <input type="file"
                                                    class="form-control form-control-sm field-damage-photo"
                                                    accept="image/*">
                                                @if ($item->hasDamagePhoto())
                                                    <small class="text-success mt-1 d-block">
                                                        <i class="bi bi-check-circle"></i> Foto: {{ $item->damage_photo }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    @if (!$inspection->isClosed())
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success save-item"
                                                title="Simpan">
                                                <i class="bi bi-check-lg save-icon"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex justify-content-center py-3">
                    {{ $items->links('pagination::bootstrap-5') }}
                </div>
            </div>
    </div>

    {{-- Action Buttons --}}
    @if (!$inspection->isClosed())
        <div class="d-flex gap-2">
            <button type="submit" name="action" value="submit" class="btn btn-success px-4"
                onclick="return confirm('Submit inspection untuk approval?\nPastikan semua data sudah benar.')">
                <i class="bi bi-send me-1"></i> Submit for Approval
            </button>
        </div>
    @else
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            Inspection ini sudah <strong>CLOSED</strong>.
            Approved by: <strong>{{ $inspection->approver?->name ?? '-' }}</strong>
            pada {{ $inspection->approved_at?->format('d M Y H:i') ?? '-' }}
        </div>
    @endif
    </form>
@endsection

@section('scripts')
    <script>
        const inspectionId = {{ $inspection->id }};
        const csrf = '{{ csrf_token() }}';

        let searchTimer;
        const searchInput = document.getElementById('searchComponent');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => document.getElementById('searchForm').submit(), 400);
            });
        }

        document.querySelectorAll('.item-row').forEach(function(row) {
            const actualInput = row.querySelector('.field-actual-qty');
            const statusSel = row.querySelector('.field-status');
            const damageWrap = row.querySelector('.field-damage-wrap');

            if (actualInput) {
                actualInput.addEventListener('input', function() {
                    const expected = parseInt(this.dataset.expected);
                    const actual = parseInt(this.value) || 0;
                    const short = Math.max(0, expected - actual);

                    row.querySelector('.field-short-qty').innerHTML = short > 0 ?
                        `<span class="text-warning fw-bold">${short}</span>` :
                        `<span class="text-muted">0</span>`;

                    if (!statusSel) return;
                    if (actual < expected) {
                        statusSel.value = 'SHORT';
                        toggleDamage(damageWrap, 'SHORT');
                    } else if (statusSel.value === 'SHORT' && actual >= expected) {
                        statusSel.value = 'OK';
                        toggleDamage(damageWrap, 'OK');
                    }
                });
            }

            if (statusSel) {
                statusSel.addEventListener('change', function() {
                    toggleDamage(damageWrap, this.value);
                });
            }

            const saveBtn = row.querySelector('.save-item');
            if (saveBtn) {
                saveBtn.addEventListener('click', async function() {
                    const icon = saveBtn.querySelector('.save-icon');
                    const original = icon.className;
                    icon.className = 'spinner-border spinner-border-sm';
                    saveBtn.disabled = true;

                    const fd = new FormData();
                    fd.append('actual_qty', actualInput.value);
                    fd.append('status', statusSel.value);
                    fd.append('damage_remark', row.querySelector('.field-damage-remark')?.value ?? '');
                    const photoInput = row.querySelector('.field-damage-photo');
                    if (photoInput && photoInput.files[0]) fd.append('photo', photoInput.files[0]);
                    fd.append('_method', 'PUT');
                    fd.append('_token', csrf);

                    try {
                        const res = await fetch(`/inspection/${inspectionId}/items/${row.dataset.id}`, {
                            method: 'POST', // FormData + Laravel needs POST + _method spoof for PUT with files
                            headers: {
                                'Accept': 'application/json'
                            },
                            body: fd,
                        });
                        const data = await res.json();
                        row.classList.add(res.ok && data.success ? 'table-success' : 'table-danger');
                        setTimeout(() => row.classList.remove('table-success', 'table-danger'), 800);
                        if (!res.ok) alert(data.message || 'Gagal menyimpan item.');
                    } catch (err) {
                        row.classList.add('table-danger');
                        setTimeout(() => row.classList.remove('table-danger'), 800);
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        icon.className = original;
                        saveBtn.disabled = false;
                    }
                });
            }
        });

        function toggleDamage(div, status) {
            if (div) div.style.display = (status === 'DAMAGE') ? '' : 'none';
        }
        // document.querySelectorAll('.actual-qty').forEach(function(input) {
        //     input.addEventListener('input', function() {
        //         const code = this.dataset.code;
        //         const expected = parseInt(this.dataset.expected);
        //         const actual = parseInt(this.value) || 0;
        //         const short = Math.max(0, expected - actual);

        //         // Update short qty display
        //         const shortCell = document.getElementById('short-' + code);
        //         if (shortCell) {
        //             shortCell.innerHTML = short > 0 ?
        //                 '<span class="text-warning fw-bold">' + short + '</span>' :
        //                 '<span class="text-muted">0</span>';
        //         }

        //         // Auto-set status to SHORT if actual < expected
        //         const statusSel = document.getElementById('status-' + code);
        //         if (!statusSel) return;

        //         if (actual < expected) {
        //             statusSel.value = 'SHORT';
        //             toggleDamage(code, 'SHORT');
        //         } else if (statusSel.value === 'SHORT' && actual >= expected) {
        //             statusSel.value = 'OK';
        //             toggleDamage(code, 'OK');
        //         }
        //     });
        // });

        // document.querySelectorAll('.status-select').forEach(function(sel) {
        //     sel.addEventListener('change', function() {
        //         toggleDamage(this.dataset.code, this.value);
        //     });
        // });

        // function toggleDamage(code, status) {
        //     const div = document.getElementById('damage-' + code);
        //     if (div) div.style.display = (status === 'DAMAGE') ? '' : 'none';
        // }
    </script>
@endsection
