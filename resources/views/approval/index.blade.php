@extends('layouts.app')
@section('page-title', 'Approval')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Approval</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    Inspection yang menunggu persetujuan
                </p>
            </div>

            <form method="GET" action="{{ route('approval.index') }}" id="searchForm">
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
                            <th style="font-size:12px;" class="px-4">#</th>
                            <th style="font-size:12px;">Inspection No</th>
                            <th style="font-size:12px;">Container/Receiving No</th>
                            <th style="font-size:12px;">Model</th>
                            <th style="font-size:12px;">Inspector</th>
                            <th style="font-size:12px;">Inspected At</th>
                            <th style="font-size:12px;">VIN</th>
                            <th style="font-size:12px;">Status</th>
                            <th style="font-size:12px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $i => $ins)
                            <tr>
                                <td class="px-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>
                                <td class="fw-semibold" style="font-size:13px;">{{ $ins['inspection_no'] }}</td>
                                <td style="font-size:13px;">
                                    {{ $ins['receiving']['container_no'] }}/{{ $ins['receiving']['receiving_no'] }}</td>
                                <td style="font-size:13px;">{{ $ins['receiving']['ckdModel']['code'] }}</td>
                                <td style="font-size:13px;">{{ $ins['inspector']['name'] }}</td>
                                <td style="font-size:13px;">{{ $ins['inspected_at'] }}</td>
                                <td style="font-size:13px;">{{ $ins['vin'] }}</td>
                                <td>
                                    <span class="badge badge-{{ $ins['status'] }}">
                                        {{ str_replace('_', ' ', $ins['status']) }}
                                    </span>
                                </td>
                                <td>

                                    <div class="d-flex gap-1">
                                        <a href="{{ route('inspection.show', $ins['id']) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($ins['status'] != 'CLOSED')
                                            <button class="btn btn-sm btn-success btnApprove" data-id="{{ $ins['id'] }}"
                                                data-no="{{ $ins['inspection_no'] }}" data-bs-toggle="modal"
                                                data-bs-target="#approveModal">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <form method="POST" action="{{ route('approval.action', $ins['id']) }}"
                                                onsubmit="return confirm('Reject inspection ini? Akan dikembalikan ke Inspector.')">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-patch-check fs-3 d-block mb-2 opacity-25"></i>
                                    Tidak ada inspection yang menunggu approval.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="card-footer bg-white d-flex justify-content-center py-3">
                    {{ $pending->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="approveForm">
                @csrf

                <input type="hidden" name="action" value="approve">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Approve Inspection
                        </h5>

                        <button class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">
                                Inspection No
                            </label>

                            <input class="form-control" id="inspectionNo" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                VIN
                            </label>

                            <input type="text" name="vin" class="form-control" required maxlength="17"
                                placeholder="Input VIN">
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                            Cancel
                        </button>

                        <button class="btn btn-success">
                            Approve
                        </button>

                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementById('searchComponent').addEventListener('input', function() {
            document.getElementById('searchForm').submit();
        });
        document.querySelectorAll('.btnApprove').forEach(btn => {

            btn.addEventListener('click', function() {

                const id = this.dataset.id;
                const no = this.dataset.no;

                document.getElementById('inspectionNo').value = no;

                document.getElementById('approveForm').action =
                    "{{ url('approval') }}/" + id;
            });

        });
    </script>
@endsection
