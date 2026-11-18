<?php

namespace App\Http\Controllers;

use App\Enums\Level;
use App\Enums\ResourceType;
use App\Models\Career;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'type' => ['nullable', Rule::enum(ResourceType::class)],
            'level' => ['nullable', Rule::enum(Level::class)],
            'language' => ['nullable', Rule::in(['es', 'en'])],
            'career' => ['nullable', 'string', 'max:100'],
            'free' => ['nullable', 'boolean'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $resources = Resource::query()
            ->published()
            ->with('careers:id,name,slug')
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['level'] ?? null, fn ($query, $level) => $query->where('level', $level))
            ->when($filters['language'] ?? null, fn ($query, $language) => $query->where('language', $language))
            ->when($filters['career'] ?? null, fn ($query, $slug) => $query->whereRelation('careers', 'slug', $slug))
            ->when($request->boolean('free'), fn ($query) => $query->where('is_free', true))
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(
                fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")
            ))
            ->orderBy('title')
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('resources/index', [
            'resources' => $resources,
            'careers' => Career::query()->published()->orderBy('name')->get(['id', 'name', 'slug']),
            'filters' => $filters,
        ]);
    }
}
