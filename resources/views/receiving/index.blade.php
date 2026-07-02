@extends('layouts.app')
@section('page-title', 'Receiving')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Receiving</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Daftar penerimaan CKD Kit</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="{{ route('receiving.index') }}" id="searchForm">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="searchComponent" class="form-control border-start-0 ps-0"
                        placeholder="Cari Container/Receiving..." value="{{ request('search') }}">
                </div>
            </form>
            <a href="{{ route('receiving.create') }}" class="btn btn-dark">
                <i class="bi bi-plus-lg me-1"></i> Add Receiving
            </a>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;">Receiving No</th>
                            <th style="font-size:12px;">Container No</th>
                            <th style="font-size:12px;">Model</th>
                            <th style="font-size:12px;">Receive Date</th>
                            <th style="font-size:12px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receivings as $i => $rcv)
                            <tr>
                                <td class="fw-semibold" style="font-size:13px;">{{ $rcv['receiving_no'] }}</td>
                                <td style="font-size:13px;">{{ $rcv['container_no'] }}</td>
                                <td style="font-size:13px;">{{ $rcv['ckdModel']['code'] }}</td>
                                <td style="font-size:13px;">{{ $rcv['receive_date']->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $rcv['status'] }}">{{ $rcv['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-25"></i>
                                    Belum ada data receiving.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="card-footer bg-white d-flex justify-content-center py-3">
                    {{ $receivings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        let searchTimeout;

        document.getElementById('searchComponent').addEventListener('input', function() {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 1500); // Wait 1500ms after the last keystroke
        });
    </script>
@endsection
