<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CkdModel;
use App\Models\Receiving;
use App\Models\Inspection;
use App\Models\InspectionItem;

class ReceivingController extends Controller
{
    public function index()
    {
        $receivings = Receiving::with(['ckdModel', 'createdBy'])
                               ->latest()
                               ->get();
        // dd($receivings->first()->ckdModel->toArray());
        return view('receiving.index', compact('receivings'));
    }

    public function create()
    {
        $models = CkdModel::active()->orderBy('code')->get();

        return view('receiving.create', compact('models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'container_no' => 'required|string|max:50',
            'ckd_model_id' => 'required|exists:ckd_models,id',
        ]);

        DB::transaction(function () use ($request) {
            // Create Receiving
            $receiving = Receiving::create([
                'receiving_no' => Receiving::generateNo(),
                'container_no' => $request->container_no,
                'ckd_model_id' => $request->ckd_model_id,
                'receive_date' => now()->toDateString(),
                'status'       => Receiving::STATUS_INSPECTION_OPEN,
                'created_by'   => session('user.id'),
            ]);

            // Create Inspection
            $inspection = Inspection::create([
                'inspection_no' => Inspection::generateNo(),
                'receiving_id'  => $receiving->id,
                'status'        => Inspection::STATUS_OPEN,
                'inspector_id'  => session('user.id'),
                'inspected_at' => now(),
            ]);

            // Create one InspectionItem per component (snapshot)
            $components = $receiving->ckdModel->activeComponents;

            foreach ($components as $component) {
                InspectionItem::create([
                    'inspection_id'  => $inspection->id,
                    'component_id'   => $component->id,
                    'component_code' => $component->code,
                    'component_name' => $component->name,
                    'expected_qty'   => $component->expected_qty,
                    'actual_qty'     => null,
                    'short_qty'      => 0,
                    'status'         => InspectionItem::STATUS_OK,
                ]);
            }

            session()->flash(
                'success',
                "Receiving {$receiving->receiving_no} berhasil dibuat & Inspection {$inspection->inspection_no} di-generate."
            );
        });

        return redirect()->route('receiving.index');
    }
}
