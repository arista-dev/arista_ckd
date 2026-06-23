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
        $query = Inspection::with([
                                'receiving.ckdModel',
                                'inspector',
                            ])
                            ->latest();

        // Inspector only sees their own workload (OPEN / WAITING_APPROVAL)
        if (session('user.role') === 'inspector') {
            $query->whereIn('status', [
                Inspection::STATUS_OPEN,
                Inspection::STATUS_WAITING_APPROVAL,
            ]);
        }

        $inspections = $query->get();

        return view('inspection.index', compact('inspections'));
    }

    public function show(string $id)
    {
        $inspection = Inspection::with([
                                    'receiving.ckdModel',
                                    'inspector',
                                    'approver',
                                    'items.component',
                                ])
                                ->findOrFail($id);

        return view('inspection.show', compact('inspection'));
    }

    public function update(Request $request, string $id)
    {
        $inspection = Inspection::with('items')->findOrFail($id);

        // Guard: cannot edit a CLOSED inspection
        if ($inspection->isClosed()) {
            return back()->withErrors(['error' => 'Inspection sudah CLOSED dan tidak dapat diubah.']);
        }

        $action = $request->input('action', 'save'); // 'save' | 'submit'

        DB::transaction(function () use ($request, $inspection, $action) {

            foreach ($inspection->items as $item) {
                $code = $item->component_code;

                $actualQty = (int) $request->input("actual_qty_{$code}", $item->expected_qty);
                $status    = $request->input("status_{$code}", InspectionItem::STATUS_OK);

                // Auto-SHORT: override status if actual < expected
                if ($actualQty < $item->expected_qty) {
                    $status = InspectionItem::STATUS_SHORT;
                }

                $shortQty     = max(0, $item->expected_qty - $actualQty);
                $damageRemark = null;
                $damagePhoto  = $item->damage_photo; // keep existing photo by default

                if ($status === InspectionItem::STATUS_DAMAGE) {
                    $damageRemark = $request->input("damage_remark_{$code}");

                    // Handle photo upload
                    $photoKey = "photo_{$code}";
                    if ($request->hasFile($photoKey) && $request->file($photoKey)->isValid()) {
                        $file     = $request->file($photoKey);
                        $filename = strtoupper($code) . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('public/damage', $filename);
                        $damagePhoto = $filename;
                    }
                } else {
                    // Clear damage fields when status changes away from DAMAGE
                    $damageRemark = null;
                    $damagePhoto  = null;
                }

                $item->update([
                    'actual_qty'    => $actualQty,
                    'short_qty'     => $shortQty,
                    'status'        => $status,
                    'damage_remark' => $damageRemark,
                    'damage_photo'  => $damagePhoto,
                ]);
            }

            // Update inspection-level fields
            $inspectionUpdates = [
                'inspector_id' => session('user.id'),
                'inspected_at' => now(),
            ];

            if ($action === 'submit') {
                $inspectionUpdates['status'] = Inspection::STATUS_WAITING_APPROVAL;
            }

            $inspection->update($inspectionUpdates);
        });

        $msg = $action === 'submit'
            ? 'Inspection berhasil disubmit, menunggu approval Supervisor.'
            : 'Inspection berhasil disimpan sebagai draft.';

        return redirect()->route('inspection.show', $id)->with('success', $msg);
    }
}
