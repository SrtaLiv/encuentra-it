<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CareerRequest;
use App\Models\Area;
use App\Models\Career;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CareerController extends Controller
{
    use SyncsChildren;

    public function index(): Response
    {
        $careers = Career::query()
            ->with('area:id,name')
            ->withCount(['steps', 'challenges', 'resources'])
            ->orderBy('name')
            ->get(['id', 'area_id', 'name', 'slug', 'coding_level', 'is_published']);

        return Inertia::render('admin/careers/index', ['careers' => $careers]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(CareerRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $career = Career::create(Arr::except($request->validated(), ['steps', 'challenges']));
            $this->syncRelations($career, $request);
        });

        return to_route('admin.careers.index')->with('success', 'Carrera creada.');
    }

    public function edit(Career $career): Response
    {
        $career->load(['steps', 'challenges']);
        $career->challenges->each->makeVisible('solution');

        return $this->form($career);
    }

    public function update(CareerRequest $request, Career $career): RedirectResponse
    {
        DB::transaction(function () use ($request, $career) {
            $career->update(Arr::except($request->validated(), ['steps', 'challenges']));
            $this->syncRelations($career, $request);
        });

        return to_route('admin.careers.index')->with('success', 'Carrera actualizada.');
    }

    public function destroy(Career $career): RedirectResponse
    {
        $career->delete();

        return to_route('admin.careers.index')->with('success', 'Carrera eliminada.');
    }

    private function form(?Career $career): Response
    {
        return Inertia::render('admin/careers/form', [
            'career' => $career,
            'areas' => Area::query()->orderBy('position')->get(['id', 'name']),
        ]);
    }

    // only sync lists that were sent, so a partial request never wipes them
    private function syncRelations(Career $career, CareerRequest $request): void
    {
        if ($request->has('steps')) {
            $this->syncSteps($career, $request->validated('steps', []));
        }

        if ($request->has('challenges')) {
            $this->syncChallenges($career, $request->validated('challenges', []));
        }
    }

    private function syncSteps(Career $career, array $steps): void
    {
        $this->syncChildren($career->steps(), $steps, fn (array $step, int $index) => [
            'level' => $step['level'],
            'title' => $step['title'],
            'description' => $step['description'] ?? null,
            'position' => $index,
        ]);
    }

    private function syncChallenges(Career $career, array $challenges): void
    {
        $this->syncChildren($career->challenges(), $challenges, fn (array $challenge, int $index) => [
            'level' => $challenge['level'],
            'title' => $challenge['title'],
            'description' => $challenge['description'],
            'deliverable' => $challenge['deliverable'],
            'ai_mode' => $challenge['ai_mode'],
            'solution' => $challenge['solution'] ?? null,
            'guide' => $this->cleanGuide($challenge['guide'] ?? null),
            'position' => $index,
        ]);
    }

    // the editor sends one textarea line per item, so drop blank lines and an all-empty guide
    private function cleanGuide(?array $guide): ?array
    {
        $clean = [];

        foreach (['before', 'steps', 'hints'] as $key) {
            $clean[$key] = array_values(array_filter(array_map(fn ($line) => trim((string) $line), $guide[$key] ?? []), fn ($line) => $line !== ''));
        }

        return array_filter($clean) ? $clean : null;
    }
}
