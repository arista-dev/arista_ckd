<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CkdModel;
use App\Models\InspectionItem;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $models   = CkdModel::active()->orderBy('code')->get();
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $modelId  = $request->input('ckd_model_id');

        $items = $this->buildQuery($dateFrom, $dateTo, $modelId)->get();

        return view('report.index', compact('items', 'models', 'dateFrom', 'dateTo', 'modelId'));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $modelId  = $request->input('ckd_model_id');

        $items = $this->buildQuery($dateFrom, $dateTo, $modelId)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inspection_report.csv"',
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens correctly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Inspection No',
                'Receiving No',
                'Model',
                'Inspector',
                'Inspected At',
                'Component',
                'Expected Qty',
                'Actual Qty',
                'Short Qty',
                'Status',
                'Damage Remark',
            ]);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->inspection->inspection_no,
                    $item->inspection->receiving->receiving_no,
                    $item->inspection->receiving->ckdModel->code,
                    $item->inspection->inspector->name ?? '-',
                    $item->inspection->inspected_at?->format('Y-m-d H:i:s'),
                    $item->component_name,
                    $item->expected_qty,
                    $item->actual_qty ?? '-',
                    $item->short_qty,
                    $item->status,
                    $item->damage_remark ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Shared Query Builder ──────────────────────────────────────────────────

    private function buildQuery(?string $dateFrom, ?string $dateTo, ?string $modelId)
    {
        $query = InspectionItem::with([
            'inspection.receiving.ckdModel',
            'inspection.inspector',
        ])
        ->whereHas('inspection', function ($q) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $q->whereDate('inspected_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $q->whereDate('inspected_at', '<=', $dateTo);
            }
        });

        if ($modelId) {
            $query->whereHas('inspection.receiving', function ($q) use ($modelId) {
                $q->where('ckd_model_id', $modelId);
            });
        }

        return $query->orderBy('inspection_id')->orderBy('id');
    }
}
