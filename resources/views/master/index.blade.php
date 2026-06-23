@extends('layouts.app')
@section('page-title', 'Master Model')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Master Model CKD</h5>
        <p class="text-muted mb-0" style="font-size:13px;">Kelola model kendaraan dan komponen CKD Kit</p>
    </div>
    <a href="{{ route('master.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-lg me-1"></i> Tambah Model
    </a>
</div>

@if($errors->has('delete'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first('delete') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    @forelse($ckdModels as $model)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-dark fs-6 mb-1">{{ $model->code }}</span>
                        <h6 class="fw-bold mb-0">{{ $model->name }}</h6>
                        @if($model->description)
                            <small class="text-muted">{{ $model->description }}</small>
                        @endif
                    </div>
                    <span class="badge {{ $model->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $model->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <div class="text-center">
                        <div class="fw-bold text-primary fs-5">{{ $model->components_count }}</div>
                        <div class="text-muted" style="font-size:11px;">Komponen</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-secondary fs-5">{{ $model->receivings_count }}</div>
                        <div class="text-muted" style="font-size:11px;">Receiving</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                <div class="d-flex gap-2">
                    <a href="{{ route('master.show', $model) }}"
                       class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <a href="{{ route('master.edit', $model) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if($model->receivings_count === 0)
                    <form method="POST" action="{{ route('master.destroy', $model) }}"
                          onsubmit="return confirm('Hapus model {{ $model->code }}? Semua komponen juga akan dihapus.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-25"></i>
                <p class="mb-2">Belum ada model CKD.</p>
                <a href="{{ route('master.create') }}" class="btn btn-dark btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Model Pertama
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
