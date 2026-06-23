@extends('layouts.app')
@section('page-title', 'Inspection')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1">Inspection</h5>
    <p class="text-muted mb-0" style="font-size:13px;">Daftar inspection CKD Kit</p>
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
                        <th style="font-size:12px;">Status</th>
                        <th style="font-size:12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $i => $ins)
                    <tr>
                        <td class="px-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>
                        <td class="fw-semibold" style="font-size:13px;">{{ $ins->inspection_no }}</td>
                        <td style="font-size:13px;">{{ $ins->receiving->receiving_no }}</td>
                        <td style="font-size:13px;">{{ $ins->receiving->ckdModel->code }}</td>
                        <td style="font-size:13px;">{{ $ins->inspector?->name ?? '-' }}</td>
                        <td style="font-size:13px;">
                            {{ $ins->inspected_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $ins->status }}">{{ $ins->status }}</span>
                        </td>
                        <td>
                            @if($ins->isEditable())
                                <a href="{{ route('inspection.show', $ins->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i> Inspect
                                </a>
                            @else
                                <a href="{{ route('inspection.show', $ins->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard fs-3 d-block mb-2 opacity-25"></i>
                            Belum ada data inspection.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
