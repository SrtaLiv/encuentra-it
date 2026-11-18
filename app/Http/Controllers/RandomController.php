<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Challenge;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RandomController extends Controller
{
    private const TYPES = ['carrera', 'recurso', 'desafio'];

    public function __invoke(Request $request): Response
    {
        $filters = $request->validate([
            'tipo' => ['nullable', Rule::in(self::TYPES)],
            'sin' => ['nullable', 'integer'],
        ]);

        $type = $filters['tipo'] ?? Arr::random(self::TYPES);
        $exceptId = isset($filters['tipo']) ? ($filters['sin'] ?? null) : null;

        return Inertia::render('random', [
            'mode' => $filters['tipo'] ?? null,
            'type' => $type,
            'item' => $this->pick($type, $exceptId),
        ]);
    }

    private function pick(string $type, ?int $exceptId): ?Model
    {
        $query = match ($type) {
            'carrera' => Career::query()
                ->published()
                ->with('area:id,name,slug,icon')
                ->withCount('challenges'),
            'recurso' => Resource::query()
                ->published()
                ->with(['careers' => fn ($query) => $query->published()->select('careers.id', 'name', 'slug')]),
            'desafio' => Challenge::query()
                ->whereRelation('career', 'is_published', true)
                ->with('career:id,name,slug'),
        };

        // skip the item just shown, unless it is the only one left
        return (clone $query)->when($exceptId, fn ($query, $id) => $query->whereKeyNot($id))->inRandomOrder()->first()
            ?? $query->inRandomOrder()->first();
    }
}
