<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Challenge;
use App\Models\QuizResult;
use App\Models\RoadmapStep;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $this->claimGuestResult($request);

        $latestResult = $user->quizResults()->latest()->first();
        $topCareers = [];

        if ($latestResult) {
            $scores = $latestResult->scores;
            arsort($scores);
            $topIds = array_slice(array_keys($scores), 0, 3);

            $topCareers = Career::query()
                ->published()
                ->whereIn('id', $topIds)
                ->get(['id', 'name', 'slug', 'summary'])
                ->map(fn (Career $career) => [...$career->toArray(), 'score' => $latestResult->scores[$career->id]])
                ->sortByDesc('score')
                ->values();
        }

        $completedByCareer = RoadmapStep::query()
            ->whereRelation('completedBy', 'users.id', $user->id)
            ->selectRaw('career_id, count(*) as completed')
            ->groupBy('career_id')
            ->pluck('completed', 'career_id');

        $progress = Career::query()
            ->whereIn('id', $completedByCareer->keys())
            ->withCount('steps')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Career $career) => [
                'name' => $career->name,
                'slug' => $career->slug,
                'completed' => (int) $completedByCareer[$career->id],
                'total' => $career->steps_count,
            ]);

        $portfolio = $user->completedChallenges()
            ->with('career:id,name,slug')
            ->orderByPivot('created_at', 'desc')
            ->get(['challenges.id', 'challenges.career_id', 'challenges.title'])
            ->map(fn (Challenge $challenge) => [
                'id' => $challenge->id,
                'title' => $challenge->title,
                'career' => $challenge->career->only('name', 'slug'),
                'evidence_url' => $challenge->pivot->evidence_url,
                'decision' => $challenge->pivot->reflection['decision'] ?? null,
            ]);

        return Inertia::render('dashboard', [
            'portfolio' => $portfolio,
            'latestResult' => $latestResult ? ['uuid' => $latestResult->uuid, 'created_at' => $latestResult->created_at] : null,
            'topCareers' => $topCareers,
            'progress' => $progress,
        ]);
    }

    // links a test taken before signing up to the new account
    private function claimGuestResult(Request $request): void
    {
        $uuid = $request->session()->pull('quiz_result');

        if (! $uuid) {
            return;
        }

        QuizResult::query()
            ->where('uuid', $uuid)
            ->whereNull('user_id')
            ->update(['user_id' => $request->user()->id]);
    }
}
