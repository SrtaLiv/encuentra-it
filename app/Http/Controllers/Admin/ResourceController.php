<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceRequest;
use App\Models\Career;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->string('q')->trim()->limit(100, '');

        $resources = Resource::query()
            ->withCount('careers')
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('url', 'like', "%{$search}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('admin/resources/index', [
            'resources' => $resources,
            'search' => $search,
        ]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(ResourceRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $resource = Resource::create(Arr::except($request->validated(), 'career_ids'));
            $resource->careers()->sync($request->validated('career_ids', []));
        });

        return to_route('admin.resources.index')->with('success', 'Recurso creado.');
    }

    public function edit(Resource $resource): Response
    {
        return $this->form($resource);
    }

    public function update(ResourceRequest $request, Resource $resource): RedirectResponse
    {
        DB::transaction(function () use ($request, $resource) {
            $resource->update(Arr::except($request->validated(), 'career_ids'));
            $resource->careers()->sync($request->validated('career_ids', []));
        });

        return to_route('admin.resources.index')->with('success', 'Recurso actualizado.');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $resource->delete();

        return to_route('admin.resources.index')->with('success', 'Recurso eliminado.');
    }

    private function form(?Resource $resource): Response
    {
        return Inertia::render('admin/resources/form', [
            'resource' => $resource ? [...$resource->toArray(), 'career_ids' => $resource->careers()->pluck('careers.id')] : null,
            'careers' => Career::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
