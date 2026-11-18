<?php

namespace App\Http\Controllers;

use App\Enums\CodingLevel;
use App\Models\Area;
use App\Models\Career;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CareerController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'area' => ['nullable', 'string', 'max:100'],
            'coding' => ['nullable', Rule::enum(CodingLevel::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $careers = Career::query()
            ->published()
            ->with('area:id,name,slug,icon')
            ->withCount('challenges')
            ->when($filters['area'] ?? null, fn ($query, $slug) => $query->whereRelation('area', 'slug', $slug))
            ->when($filters['coding'] ?? null, fn ($query, $level) => $query->where('coding_level', $level))
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(
                fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('summary', 'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->get(['id', 'area_id', 'name', 'slug', 'summary', 'coding_level']);

        return Inertia::render('careers/index', [
            'careers' => $careers,
            'areas' => Area::query()->orderBy('position')->get(['id', 'name', 'slug', 'icon']),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, Career $career): Response
    {
        abort_unless($career->is_published, 404);

        $career->load([
            'area:id,name,slug,icon',
            'steps',
            'challenges',
            'resources' => fn ($query) => $query->published()->orderBy('type')->orderBy('title'),
        ]);

        $completedStepIds = $request->user()
            ?->completedSteps()
            ->where('career_id', $career->id)
            ->pluck('roadmap_steps.id') ?? [];

        // challenge id => what the user submitted when completing it
        $completedChallenges = $request->user()
            ?->completedChallenges()
            ->where('career_id', $career->id)
            ->get(['challenges.id'])
            ->mapWithKeys(fn (Challenge $challenge) => [$challenge->id => [
                'evidence_url' => $challenge->pivot->evidence_url,
                'reflection' => $challenge->pivot->reflection,
            ]]) ?? collect();

        // the solution is revealed only after completing, so nobody can peek first
        $career->challenges->each(function (Challenge $challenge) use ($completedChallenges) {
            $challenge->setAttribute('has_solution', filled($challenge->solution));

            if ($completedChallenges->has($challenge->id)) {
                $challenge->makeVisible('solution');
            }
        });

        return Inertia::render('careers/show', [
            'career' => $career,
            'completedStepIds' => $completedStepIds,
            'completedChallenges' => (object) $completedChallenges->all(),
        ]);
    }
}
