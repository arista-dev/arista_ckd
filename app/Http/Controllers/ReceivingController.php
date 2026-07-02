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
    public function index(Request $request)
    {
        $query = Receiving::active()
            ->with(['ckdModel', 'createdBy'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('container_no', 'like', "%{$search}%")->orWhere('receiving_no', 'like', "%{$search}%");
            });
        }

        $receivings = $query->orderByDesc('created_at')->orderByDesc('container_no')->paginate(10)->withQueryString();

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
            'total_receiving' => 'required|integer|min:1',
            'ckd_model_id' => 'required|exists:ckd_models,id',
        ]);

        $model = CkdModel::with('activeComponents')->findOrFail($request->ckd_model_id);
        $components = $model->activeComponents;
        $now = now();

        DB::transaction(function () use ($request, $model, $components, $now) {
            for ($i = 0; $i < $request->total_receiving; $i++) {
                $receiving = Receiving::create([
                    'receiving_no' => Receiving::generateNo(),
                    'container_no' => $request->container_no . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'ckd_model_id' => $model->id,
                    'receive_date' => $now,
                    'status' => Receiving::STATUS_INSPECTION_OPEN,
                    'created_by' => session('user.id'),
                ]);

                $inspection = Inspection::create([
                    'inspection_no' => Inspection::generateNo(),
                    'receiving_id' => $receiving->id,
                    'status' => Inspection::STATUS_OPEN,
                    'inspector_id' => session('user.id'),
                    'inspected_at' => $now,
                ]);

                $items = [];

                foreach ($components as $component) {
                    $items[] = [
                        'inspection_id' => $inspection->id,
                        'component_id' => $component->id,
                        'component_code' => $component->code,
                        'component_name' => $component->name,
                        'expected_qty' => $component->expected_qty,
                        'actual_qty' => $component->expected_qty,
                        'short_qty' => 0,
                        'status' => InspectionItem::STATUS_OK,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                InspectionItem::insert($items);
            }

            session()->flash('success', "Receiving {$receiving->receiving_no} berhasil dibuat & Inspection {$inspection->inspection_no} di-generate.");
        });

        return redirect()->route('receiving.index');
    }

    public function destroy(Receiving $receiving)
    {
        if ($receiving->deleted) {
            return back()->with('error', 'Receiving already deleted.');
        }

        $receiving->update([
            'deleted' => true,
        ]);

        return redirect()->route('receiving.index')->with('success', 'Receiving berhasil dihapus.');
    }
}
