<?php

namespace App\Http\Controllers;

use App\Models\Receiving;
use App\Models\Inspection;
use App\Models\InspectionItem;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_receiving'  => Receiving::count(),
            'total_inspection' => Inspection::count(),
            'total_shortage'   => InspectionItem::where('status', InspectionItem::STATUS_SHORT)->count(),
            'total_damage'     => InspectionItem::where('status', InspectionItem::STATUS_DAMAGE)->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
