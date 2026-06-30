<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CkdModel;
use App\Models\Component;
use Illuminate\Validation\Rule;

class MasterModelController extends Controller
{
    // ─── CKD Model CRUD ───────────────────────────────────────────────────────

    public function index()
    {
        $ckdModels = CkdModel::withCount(['components', 'receivings'])
                             ->orderBy('code')
                             ->get();

        return view('master.index', compact('ckdModels'));
    }

    public function create()
    {
        return view('master.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:20|unique:ckd_models,code',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            // Components (at least 1 required)
            'components'              => 'required|array|min:1',
            'components.*.code'       => 'required|string|max:20',
            'components.*.name'       => 'required|string|max:100',
            'components.*.expected_qty' => 'required|integer|min:1',
        ], [
            'code.unique'           => 'Kode model sudah digunakan.',
            'components.required'   => 'Minimal 1 komponen harus ditambahkan.',
            'components.min'        => 'Minimal 1 komponen harus ditambahkan.',
        ]);

        DB::transaction(function () use ($request) {
            $model = CkdModel::create([
                'code'        => strtoupper(trim($request->code)),
                'name'        => $request->name,
                'description' => $request->description,
                'is_active'   => true,
            ]);

            foreach ($request->components as $comp) {
                Component::create([
                    'ckd_model_id' => $model->id,
                    'code'         => strtoupper(trim($comp['code'])),
                    'name'         => $comp['name'],
                    'expected_qty' => $comp['expected_qty'],
                    'is_active'    => true,
                ]);
            }

            session()->flash('success', "Model {$model->code} berhasil ditambahkan.");
        });

        return redirect()->route('master.index');
    }

    public function show(CkdModel $master)
    {
     $components = $master->components()
    ->when(request('search'), function ($query, $search) {
        $query->where(function ($q) use ($search) {
            $q->where('code', 'ilike', "%{$search}%")
              ->orWhere('name', 'ilike', "%{$search}%");
        });
    })
    ->orderBy('code')
    ->paginate(10)
    ->withQueryString(); // keeps search-term in query string across pages, if you add server-side search later

    return view('master.show', compact('master', 'components'));
    }

    public function edit(CkdModel $master)
{
    $components = $master->components()
        ->when(request('search'), function ($query, $search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(code) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
            });
        })
        ->orderBy('code')
        ->paginate(10)
        ->withQueryString();

    return view('master.edit', compact('master', 'components'));
}

public function update(Request $request, CkdModel $master)
{
    $validated = $request->validate([
        'code' => ['required', 'string', 'max:20', Rule::unique('ckd_models', 'code')->ignore($master->id)],
        'name' => ['required', 'string', 'max:100'],
        'description' => ['nullable', 'string'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    $validated['is_active'] = $request->boolean('is_active');

    $master->update($validated);

    return redirect()->route('master.show', $master)->with('success', 'Model berhasil diperbarui.');
}

public function updateComponent(Request $request, CkdModel $master, Component $component)
{
    abort_unless($component->ckd_model_id === $master->id, 404);

    $validated = $request->validate([
        'code' => ['required', 'string', 'max:20', Rule::unique('components', 'code')
            ->where('ckd_model_id', $master->id)->ignore($component->id)],
        'name' => ['required', 'string', 'max:100'],
        'expected_qty' => ['required', 'integer', 'min:1'],
    ]);

    // lock code if it has inspection history
    if ($component->inspectionItems()->exists()) {
        unset($validated['code']);
    }

    $component->update($validated);

    return response()->json(['success' => true, 'component' => $component]);
}

public function storeComponent(Request $request, CkdModel $master)
{
    $validated = $request->validate([
        'code' => ['required', 'string', 'max:20', Rule::unique('components', 'code')->where('ckd_model_id', $master->id)],
        'name' => ['required', 'string', 'max:100'],
        'expected_qty' => ['required', 'integer', 'min:1'],
    ]);

    $component = $master->components()->create($validated);

    return response()->json(['success' => true, 'component' => $component]);
}

public function destroyComponent(CkdModel $master, Component $component)
{
    abort_unless($component->ckd_model_id === $master->id, 404);

    if ($component->inspectionItems()->exists()) {
        return response()->json(['success' => false, 'message' => 'Komponen ini punya riwayat inspeksi.'], 422);
    }

    $component->delete();

    return response()->json(['success' => true]);
}

    // public function edit(CkdModel $master)
    // {
    //     $master->load(['components' => fn ($q) => $q->orderBy('code')]);

    //     return view('master.edit', compact('master'));
    // }

    // public function update(Request $request, CkdModel $master)
    // {
    //         dd($request->all());
    //     $request->validate([
    //         'code'        => 'required|string|max:20|unique:ckd_models,code,' . $master->id,
    //         'name'        => 'required|string|max:100',
    //         'description' => 'nullable|string',
    //         'is_active'   => 'boolean',
    //         'components'              => 'required|array|min:1',
    //         'components.*.code'       => 'required|string|max:20',
    //         'components.*.name'       => 'required|string|max:100',
    //         'components.*.expected_qty' => 'required|integer|min:1',
    //     ], [
    //         'code.unique'         => 'Kode model sudah digunakan.',
    //         'components.required' => 'Minimal 1 komponen harus ada.',
    //     ]);

    

    //     // Guard: cannot deactivate model that has open receivings
    //     if (!$request->boolean('is_active')) {
    //         $openCount = $master->receivings()
    //                             ->whereIn('status', ['RECEIVED', 'INSPECTION_OPEN'])
    //                             ->count();
    //         if ($openCount > 0) {
    //             return back()->withErrors([
    //                 'is_active' => "Tidak dapat menonaktifkan model. Terdapat {$openCount} receiving yang masih aktif.",
    //             ]);
    //         }
    //     }

    //     DB::transaction(function () use ($request, $master) {
    //         $master->update([
    //             'code'        => strtoupper(trim($request->code)),
    //             'name'        => $request->name,
    //             'description' => $request->description,
    //             'is_active'   => $request->boolean('is_active', true),
    //         ]);

    //         // Sync components: delete removed ones, upsert existing/new ones
    //         $submittedIds = collect($request->components)
    //                             ->pluck('id')
    //                             ->filter()
    //                             ->values();

    //         // Delete components that were removed (only if never used in an inspection)
    //         $master->components()
    //                ->whereNotIn('id', $submittedIds)
    //                ->whereDoesntHave('inspectionItems')
    //                ->delete();

    //         // Deactivate components that were removed but have inspection history
    //         $master->components()
    //                ->whereNotIn('id', $submittedIds)
    //                ->whereHas('inspectionItems')
    //                ->update(['is_active' => false]);

    //         foreach ($request->components as $comp) {
    //             $data = [
    //                 'ckd_model_id' => $master->id,
    //                 'code'         => strtoupper(trim($comp['code'])),
    //                 'name'         => $comp['name'],
    //                 'expected_qty' => $comp['expected_qty'],
    //                 'is_active'    => true,
    //             ];

    //             if (!empty($comp['id'])) {
    //                 // Update existing
    //                 Component::where('id', $comp['id'])
    //                          ->where('ckd_model_id', $master->id)
    //                          ->update($data);
    //             } else {
    //                 // New component
    //                 Component::create($data);
    //             }
    //         }
    //     });

    //     return redirect()->route('master.show', $master)
    //                      ->with('success', "Model {$master->code} berhasil diupdate.");
    // }

    // public function destroy(CkdModel $master)
    // {
    //     // Cannot delete if has any receivings
    //     if ($master->receivings()->exists()) {
    //         return back()->withErrors([
    //             'delete' => "Model {$master->code} tidak dapat dihapus karena sudah memiliki data Receiving.",
    //         ]);
    //     }

    //     DB::transaction(function () use ($master) {
    //         $master->components()->delete();
    //         $master->delete();
    //     });

    //     return redirect()->route('master.index')
    //                      ->with('success', "Model {$master->code} berhasil dihapus.");
    // }

    // ─── Component quick-toggle active ────────────────────────────────────────

    public function toggleComponent(Request $request, CkdModel $master, Component $component)
    {
        abort_if($component->ckd_model_id !== $master->id, 404);

        $component->update(['is_active' => !$component->is_active]);

        $status = $component->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Komponen {$component->name} berhasil {$status}.");
    }
}
