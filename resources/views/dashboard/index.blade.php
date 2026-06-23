@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h5 class="fw-bold mb-1">Dashboard</h5>
        <p class="text-muted mb-0" style="font-size:13px;">Selamat datang, {{ session('user.name') }}</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:#e8f4fd;">
                    <i class="bi bi-truck fs-4" style="color:#0d6efd;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Receiving</div>
                    <div class="fw-bold fs-4">{{ $stats['total_receiving'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:#e8f9f0;">
                    <i class="bi bi-clipboard-check fs-4" style="color:#198754;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Inspection</div>
                    <div class="fw-bold fs-4">{{ $stats['total_inspection'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:#fff8e1;">
                    <i class="bi bi-exclamation-triangle fs-4" style="color:#ffc107;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Shortage</div>
                    <div class="fw-bold fs-4">{{ $stats['total_shortage'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:#fdecea;">
                    <i class="bi bi-shield-exclamation fs-4" style="color:#dc3545;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Damage</div>
                    <div class="fw-bold fs-4">{{ $stats['total_damage'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-box-seam fs-1 opacity-25"></i>
        <p class="mt-2 mb-0">CKD Inspection System — MVP v1.0</p>
    </div>
</div>
@endsection
