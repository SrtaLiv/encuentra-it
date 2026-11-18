<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AreaRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AreaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/areas/index', [
            'areas' => Area::query()->withCount('careers')->orderBy('position')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/areas/form', ['area' => null]);
    }

    public function store(AreaRequest $request): RedirectResponse
    {
        Area::create($request->validated());

        return to_route('admin.areas.index')->with('success', 'Área creada.');
    }

    public function edit(Area $area): Response
    {
        return Inertia::render('admin/areas/form', ['area' => $area]);
    }

    public function update(AreaRequest $request, Area $area): RedirectResponse
    {
        $area->update($request->validated());

        return to_route('admin.areas.index')->with('success', 'Área actualizada.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        if ($area->careers()->exists()) {
            return back()->with('error', 'No se puede borrar un área que tiene carreras. Movelas o borralas primero.');
        }

        $area->delete();

        return to_route('admin.areas.index')->with('success', 'Área eliminada.');
    }
}
