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
@if($inspection->status === 'OPEN' && $inspection->rejected_at)
<div class="alert alert-danger mb-4">
    <i class="bi bi-x-circle me-2"></i>
    <strong>Inspection di-Reject</strong> oleh {{ $inspection->rejecter?->name }} pada
    {{ $inspection->rejected_at->format('d M Y H:i') }}.
    @if($inspection->rejection_reason)
        <br><span class="ms-4">Alasan: {{ $inspection->rejection_reason }}</span>
    @endif
</div>
@endif

<form method="POST" action="{{ route('inspection.update', $inspection->id) }}"
      enctype="multipart/form-data">
@csrf

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-list-check me-2"></i>Component Checklist
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size:12px;" class="px-4">Component</th>
                        <th style="font-size:12px;" class="text-center">Expected Qty</th>
                        <th style="font-size:12px;" class="text-center">Actual Qty</th>
                        <th style="font-size:12px;" class="text-center">Short Qty</th>
                        <th style="font-size:12px;" class="text-center">Status</th>
                        <th style="font-size:12px;">Damage Info</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inspection->items as $item)
                    <tr>
                        {{-- Component Name --}}
                        <td class="px-4 fw-semibold" style="font-size:13px;">
                            {{ $item->component_name }}
                            <small class="text-muted d-block" style="font-size:11px;">
                                {{ $item->component_code }}
                            </small>
                        </td>

                        {{-- Expected --}}
                        <td class="text-center" style="font-size:13px;">
                            {{ $item->expected_qty }}
                        </td>

                        {{-- Actual --}}
                        <td class="text-center">
                            @if($inspection->isClosed())
                                <span style="font-size:13px;">{{ $item->actual_qty ?? '-' }}</span>
                            @else
                                <input type="number"
                                       name="actual_qty_{{ $item->component_code }}"
                                       class="form-control form-control-sm text-center actual-qty"
                                       style="width:80px; margin:auto;"
                                       value="{{ $item->actual_qty ?? $item->expected_qty }}"
                                       min="0"
                                       data-code="{{ $item->component_code }}"
                                       data-expected="{{ $item->expected_qty }}">
                            @endif
                        </td>

                        {{-- Short Qty --}}
                        <td class="text-center" id="short-{{ $item->component_code }}"
                            style="font-size:13px;">
                            <span class="{{ $item->short_qty > 0 ? 'text-warning fw-bold' : 'text-muted' }}">
                                {{ $item->short_qty }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @if($inspection->isClosed())
                                <span class="badge badge-{{ $item->status }}">{{ $item->status }}</span>
                            @else
                                <select name="status_{{ $item->component_code }}"
                                        class="form-select form-select-sm status-select"
                                        style="width:120px; margin:auto;"
                                        data-code="{{ $item->component_code }}"
                                        id="status-{{ $item->component_code }}">
                                    <option value="OK"
                                        {{ $item->status === 'OK'     ? 'selected' : '' }}>OK</option>
                                    <option value="SHORT"
                                        {{ $item->status === 'SHORT'  ? 'selected' : '' }}>SHORT</option>
                                    <option value="DAMAGE"
                                        {{ $item->status === 'DAMAGE' ? 'selected' : '' }}>DAMAGE</option>
                                </select>
                            @endif
                        </td>

                        {{-- Damage Info --}}
                        <td style="min-width:240px;">
                            @if($inspection->isClosed())
                                @if($item->isDamage())
                                    <small class="text-muted d-block">
                                        {{ $item->damage_remark ?? '-' }}
                                    </small>
                                    @if($item->hasDamagePhoto())
                                        <a href="{{ $item->damagePhotoUrl() }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary mt-1">
                                            <i class="bi bi-image"></i> Lihat Foto
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                @endif
                            @else
                                <div id="damage-{{ $item->component_code }}"
                                     style="{{ $item->isDamage() ? '' : 'display:none;' }}">
                                    <input type="text"
                                           name="damage_remark_{{ $item->component_code }}"
                                           class="form-control form-control-sm mb-1"
                                           placeholder="Damage remark..."
                                           value="{{ $item->damage_remark ?? '' }}">
                                    <input type="file"
                                           name="photo_{{ $item->component_code }}"
                                           class="form-control form-control-sm"
                                           accept="image/*">
                                    @if($item->hasDamagePhoto())
                                        <small class="text-success">
                                            <i class="bi bi-check-circle"></i>
                                            Foto ada: {{ $item->damage_photo }}
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Action Buttons --}}
@if(!$inspection->isClosed())
<div class="d-flex gap-2">
    <button type="submit" name="action" value="save"
            class="btn btn-outline-dark px-4">
        <i class="bi bi-save me-1"></i> Save Draft
    </button>
    <button type="submit" name="action" value="submit"
            class="btn btn-success px-4"
            onclick="return confirm('Submit inspection untuk approval Supervisor?\nPastikan semua data sudah benar.')">
        <i class="bi bi-send me-1"></i> Submit for Approval
    </button>
</div>
@else
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>
    Inspection ini sudah <strong>CLOSED</strong>.
    Approved by <strong>{{ $inspection->approver?->name ?? '-' }}</strong>
    pada {{ $inspection->approved_at?->format('d M Y H:i') ?? '-' }}.
</div>
@endif

</form>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.actual-qty').forEach(function (input) {
    input.addEventListener('input', function () {
        const code     = this.dataset.code;
        const expected = parseInt(this.dataset.expected);
        const actual   = parseInt(this.value) || 0;
        const short    = Math.max(0, expected - actual);

        // Update short qty display
        const shortCell = document.getElementById('short-' + code);
        if (shortCell) {
            shortCell.innerHTML = short > 0
                ? '<span class="text-warning fw-bold">' + short + '</span>'
                : '<span class="text-muted">0</span>';
        }

        // Auto-set status to SHORT if actual < expected
        const statusSel = document.getElementById('status-' + code);
        if (!statusSel) return;

        if (actual < expected) {
            statusSel.value = 'SHORT';
            toggleDamage(code, 'SHORT');
        } else if (statusSel.value === 'SHORT') {
            statusSel.value = 'OK';
            toggleDamage(code, 'OK');
        }
    });
});

document.querySelectorAll('.status-select').forEach(function (sel) {
    sel.addEventListener('change', function () {
        toggleDamage(this.dataset.code, this.value);
    });
});

function toggleDamage(code, status) {
    const div = document.getElementById('damage-' + code);
    if (div) div.style.display = (status === 'DAMAGE') ? '' : 'none';
}
</script>
@endsection
