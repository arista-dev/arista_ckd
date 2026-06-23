@extends('layouts.app')
@section('page-title', 'Add Receiving')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('receiving.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Add Receiving</h5>
        <p class="text-muted mb-0" style="font-size:13px;">Buat penerimaan CKD Kit baru</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('receiving.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Container No <span class="text-danger">*</span></label>
                        <input type="text" name="container_no"
                               class="form-control @error('container_no') is-invalid @enderror"
                               value="{{ old('container_no') }}"
                               placeholder="Contoh: CONT-2024-001" required>
                        @error('container_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Model <span class="text-danger">*</span></label>
                        <select name="ckd_model_id"
                                class="form-select @error('ckd_model_id') is-invalid @enderror" required>
                            <option value="">— Pilih Model —</option>
                            @foreach($models as $m)
                                <option value="{{ $m->id }}"
                                    {{ old('ckd_model_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->code }} — {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ckd_model_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info py-2" style="font-size:13px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Receiving No akan di-generate otomatis. Inspection sheet dibuat langsung setelah save.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark px-4">
                            <i class="bi bi-save me-1"></i> Save
                        </button>
                        <a href="{{ route('receiving.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
