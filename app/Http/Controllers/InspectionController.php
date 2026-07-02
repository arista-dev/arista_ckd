<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inspection;
use App\Models\InspectionItem;

class InspectionController extends Controller
{
    public function index()
    {
        $query = Inspection::query()
    ->select('inspections.*')
    ->join('receivings', 'receivings.id', '=', 'inspections.receiving_id')
    ->with(['receiving.ckdModel', 'inspector']);

        // Inspector only sees their own workload (OPEN / WAITING_APPROVAL)
        if (session('user.role') === 'inspector') {
            $query->whereIn('inspections.status', [Inspection::STATUS_OPEN, Inspection::STATUS_WAITING_APPROVAL]);
        }

        $inspections =$query->when(request('search'), function ($query, $search) {
            $search = strtolower($search);
            $query->whereHas('receiving', function ($q) use ($search) {
                $q->whereRaw('LOWER(container_no) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(receiving_no) LIKE ?', ["%{$search}%"]);
            });
        })
        ->orderByDesc('inspections.created_at')
        ->orderByDesc('receivings.container_no')
        ->paginate(10)
        ->withQueryString();

        return view('inspection.index', compact('inspections'));
    }

   public function show(string $id)
    {
        $inspection = Inspection::with(['receiving.ckdModel', 'inspector', 'approver'])
                                ->findOrFail($id);

        $items = $inspection->items()
            ->when(request('search'), function ($query, $search) {
                $search = strtolower($search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(component_code) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(component_name) LIKE ?', ["%{$search}%"]);
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('inspection.show', compact('inspection', 'items'));
    }

    public function updateItem(Request $request, Inspection $inspection, InspectionItem $item)
    {
        abort_unless($item->inspection_id === $inspection->id, 404);

        if ($inspection->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Inspection sudah CLOSED.'], 422);
        }

        $validated = $request->validate([
            'actual_qty' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:OK,SHORT,DAMAGE'],
            'damage_remark' => ['nullable', 'string'],
        ]);

        $actualQty = $validated['actual_qty'];
        $status = $validated['status'];

        // Auto-SHORT: override if actual < expected
        if ($actualQty < $item->expected_qty) {
            $status = InspectionItem::STATUS_SHORT;
        }

        $shortQty = max(0, $item->expected_qty - $actualQty);
        $damageRemark = null;
        $damagePhoto = $item->damage_photo;

        if ($status === InspectionItem::STATUS_DAMAGE || $status === InspectionItem::STATUS_SHORT) {
            $damageRemark = $validated['damage_remark'] ?? null;

            // if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            //     $file = $request->file('photo');
            //     $filename = strtoupper($item->component_code) . '_' . time() . '.' . $file->getClientOriginalExtension();
            //     $file->storeAs('public/damage', $filename);
            //     $damagePhoto = $filename;
            // }
        } else {
            $damageRemark = null;
            $damagePhoto = null;
        }

        $item->update([
            'actual_qty' => $actualQty,
            'short_qty' => $shortQty,
            'status' => $status,
            'damage_remark' => $damageRemark,
            'damage_photo' => $damagePhoto,
        ]);

        $inspection->update([
            'inspector_id' => session('user.id'),
            'inspected_at' => now(),
        ]);

        return response()->json(['success' => true, 'item' => $item, 'short_qty' => $shortQty]);
    }

    public function update(Request $request, string $id)
    {
        $inspection = Inspection::findOrFail($id);

        if ($inspection->isClosed()) {
            return back()->withErrors(['error' => 'Inspection sudah CLOSED dan tidak dapat diubah.']);
        }

        $action = $request->input('action', 'save');

        if ($action === 'submit') {
            $inspection->update(['status' => Inspection::STATUS_WAITING_APPROVAL]);
        }
        if ($action === 'cancel') {
            $inspection->update(['status' => Inspection::STATUS_OPEN]);
        }

        $msg = $action === 'submit' ? 'Inspection berhasil disubmit, menunggu approval Supervisor.' : 'Inspection berhasil disimpan sebagai draft.';

        return redirect()->route('inspection.show', $id)->with('success', $msg);
    }
}
