<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inspection;
use App\Models\Receiving;

class ApprovalController extends Controller
{
    public function index()
    {
        $pending = Inspection::with(['receiving.ckdModel', 'inspector', 'items'])
            ->latest('inspected_at')
            ->get();

        
        // dd($pending);
        return view('approval.index', compact('pending'));
    }

    public function action(Request $request, string $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'vin' => 'required_if:action,approve|max:17',
        ]);

        $inspection = Inspection::with('receiving')->findOrFail($id);

        // Guard: only WAITING_APPROVAL can be actioned
        if (!$inspection->isWaitingApproval()) {
            return back()->withErrors(['error' => 'Inspection tidak dalam status WAITING_APPROVAL.']);
        }

        DB::transaction(function () use ($request, $inspection) {
            if ($request->action === 'approve') {
                $inspection->update([
                    'vin'         => strtoupper($request->vin),
                    'status' => Inspection::STATUS_CLOSED,
                    'approved_by' => session('user.id'),
                    'approved_at' => now(),
                    // Clear any previous rejection data
                    'rejected_by' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]);

                // Close the linked Receiving
                $inspection->receiving->update([
                    'status' => Receiving::STATUS_CLOSED,
                ]);

                session()->flash('success', "Inspection {$inspection->inspection_no} berhasil di-Approve.");
            } else {
                $inspection->update([
                    'status' => Inspection::STATUS_OPEN,
                    'rejected_by' => session('user.id'),
                    'rejected_at' => now(),
                    'rejection_reason' => $request->input('rejection_reason'),
                    // Clear approval data
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                session()->flash('success', "Inspection {$inspection->inspection_no} di-Reject, dikembalikan ke Inspector.");
            }
        });

        return redirect()->route('approval.index');
    }
}
