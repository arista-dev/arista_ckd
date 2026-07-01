@extends('layouts.app')
@section('page-title', 'Inspection')

@section('content')
    <div class="card-header d-flex justify-content-between align-items-center mb-4">
        <div class ="py-3 d-flex justify-content-between align-items-center w-100">
            <div class="div">
                <h5 class="fw-bold mb-1">Inspection</h5>
                <p class="text-muted mb-0" style="font-size:13px;">Daftar inspection CKD Kit</p>
            </div>

            <form method="GET" action="{{ route('inspection.index') }}" id="searchForm">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchComponent" class="form-control border-start-0 ps-0"
                        placeholder="Cari Container/Receiving..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;">Inspection No</th>
                            {{-- <th style="font-size:12px; width: 150px;">Container No</th> --}}
                            <th style="font-size:12px;">Cont / Receiving No</th>
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

                                <td class="fw-semibold">
                                    {{ $ins->inspection_no }}
                                </td>
                                {{-- <td class ="">
                                    {{ $ins->receiving->container_no }}
                                </td> --}}
                                <td>
                                    {{ $ins->receiving->container_no ?? '-' }} / {{ $ins->receiving->receiving_no }}
                                </td>

                                <td>
                                    {{ $ins->receiving->ckdModel->code }}
                                </td>

                                <td>
                                    {{ $ins->inspector?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $ins->inspected_at?->format('Y-m-d H:i') ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge badge-{{ $ins->status }}">{{ $ins->status }}</span>
                                </td>

                                <td>
                                    @if (in_array($ins->status, ['OPEN', 'WAITING_APPROVAL']))
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
                <div class="card-footer bg-white d-flex justify-content-center py-3">
                    {{ $inspections->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('searchComponent').addEventListener('input', function() {
            document.getElementById('searchForm').submit();
        });
    </script>

@endsection
