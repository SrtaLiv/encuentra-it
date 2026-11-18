<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuizQuestionRequest;
use App\Models\Career;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuizQuestionController extends Controller
{
    use SyncsChildren;

    public function index(): Response
    {
        return Inertia::render('admin/quiz/index', [
            'questions' => QuizQuestion::query()->withCount('options')->orderBy('position')->get(),
        ]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(QuizQuestionRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $question = QuizQuestion::create($request->safe()->only('text', 'position'));
            $this->syncOptions($question, $request->validated('options'));
        });

        return to_route('admin.quiz.index')->with('success', 'Pregunta creada.');
    }

    public function edit(QuizQuestion $question): Response
    {
        return $this->form($question);
    }

    public function update(QuizQuestionRequest $request, QuizQuestion $question): RedirectResponse
    {
        DB::transaction(function () use ($request, $question) {
            $question->update($request->safe()->only('text', 'position'));
            $this->syncOptions($question, $request->validated('options'));
        });

        return to_route('admin.quiz.index')->with('success', 'Pregunta actualizada.');
    }

    public function destroy(QuizQuestion $question): RedirectResponse
    {
        $question->delete();

        return to_route('admin.quiz.index')->with('success', 'Pregunta eliminada.');
    }

    private function form(?QuizQuestion $question): Response
    {
        $question?->load('options.careers:id');

        return Inertia::render('admin/quiz/form', [
            'question' => $question ? [
                'id' => $question->id,
                'text' => $question->text,
                'position' => $question->position,
                'options' => $question->options->map(fn (QuizOption $option) => [
                    'id' => $option->id,
                    'text' => $option->text,
                    'career_ids' => $option->careers->pluck('id'),
                ]),
            ] : null,
            'careers' => Career::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function syncOptions(QuizQuestion $question, array $options): void
    {
        $options = array_values($options);

        $saved = $this->syncChildren($question->options(), $options, fn (array $option, int $index) => [
            'text' => $option['text'],
            'position' => $index,
        ]);

        foreach ($saved as $index => $option) {
            $option->careers()->sync($options[$index]['career_ids'] ?? []);
        }
    }
}
