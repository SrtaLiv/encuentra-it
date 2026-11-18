<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizResultRequest;
use App\Models\Career;
use App\Models\QuizResult;
use App\Services\QuizScorer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function show(QuizScorer $scorer): Response
    {
        $questions = $scorer->questions()->map(fn ($question) => [
            'id' => $question->id,
            'text' => $question->text,
            'options' => $question->options->map->only('id', 'text')->values(),
        ]);

        return Inertia::render('quiz/show', [
            'questions' => $questions,
        ]);
    }

    public function store(StoreQuizResultRequest $request, QuizScorer $scorer): RedirectResponse
    {
        $result = QuizResult::create([
            'user_id' => $request->user()?->id,
            'answers' => $request->optionIds(),
            'scores' => $scorer->score($request->optionIds()),
        ]);

        $request->session()->put('quiz_result', $result->uuid);

        return to_route('quiz.result', $result);
    }

    public function result(QuizResult $result): Response
    {
        $scores = $result->scores;

        $careers = Career::query()
            ->published()
            ->with('area:id,name,slug,icon')
            ->whereIn('id', array_keys($scores))
            ->get(['id', 'area_id', 'name', 'slug', 'summary', 'coding_level'])
            ->map(fn (Career $career) => [...$career->toArray(), 'score' => $scores[$career->id]])
            ->sortByDesc('score')
            ->values();

        return Inertia::render('quiz/result', [
            'uuid' => $result->uuid,
            'careers' => $careers,
        ]);
    }
}
