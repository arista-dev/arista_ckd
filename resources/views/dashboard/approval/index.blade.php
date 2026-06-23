@extends('layouts.app')
@section('page-title', 'Approval')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1">Approval</h5>
    <p class="text-muted mb-0" style="font-size:13px;">
        Inspection menunggu persetujuan
        <span class="badge bg-warning text-dark ms-1">{{ $pending->count() }}</span>
    </p>
</div>

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
                        <th style="font-size:12px;">Inspected At</th>
                        <th style="font-size:12px;" class="text-center">Short</th>
                        <th style="font-size:12px;" class="text-center">Damage</th>
                        <th style="font-size:12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $i => $ins)
                    <tr>
                        <td class="px-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>
                        <td class="fw-semibold" style="font-size:13px;">{{ $ins->inspection_no }}</td>
                        <td style="font-size:13px;">{{ $ins->receiving->receiving_no }}</td>
                        <td style="font-size:13px;">{{ $ins->receiving->ckdModel->code }}</td>
                        <td style="font-size:13px;">{{ $ins->inspector?->name ?? '-' }}</td>
                        <td style="font-size:13px;">
                            {{ $ins->inspected_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                        <td class="text-center">
                            @php $shorts = $ins->items->where('status', 'SHORT')->count() @endphp
                            @if($shorts > 0)
                                <span class="badge bg-warning text-dark">{{ $shorts }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php $damages = $ins->items->where('status', 'DAMAGE')->count() @endphp
                            @if($damages > 0)
                                <span class="badge bg-danger">{{ $damages }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- View --}}
                                <a href="{{ route('inspection.show', $ins->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- Approve --}}
                                <form method="POST" action="{{ route('approval.action', $ins->id) }}"
                                      onsubmit="return confirm('Approve inspection {{ $ins->inspection_no }}?')">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-sm btn-success">
                                        <i class="bi bi-check-lg me-1"></i>Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal-{{ $ins->id }}">
                                    <i class="bi bi-x-lg me-1"></i>Reject
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Reject Modal --}}
                    <div class="modal fade" id="rejectModal-{{ $ins->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('approval.action', $ins->id) }}">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">
                                            Reject — {{ $ins->inspection_no }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label fw-semibold">
                                            Alasan Reject <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="rejection_reason" rows="3"
                                                  class="form-control"
                                                  placeholder="Tulis alasan penolakan..."
                                                  required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-x-lg me-1"></i>Confirm Reject
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-patch-check fs-3 d-block mb-2 opacity-25"></i>
                            Tidak ada inspection yang menunggu approval.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
