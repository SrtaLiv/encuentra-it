<?php

namespace Tests\Feature;

use App\Models\QuizQuestion;
use App\Models\QuizResult;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function firstOptionAnswers(): array
    {
        return QuizQuestion::with('options')->get()
            ->mapWithKeys(fn ($question) => [$question->id => $question->options->first()->id])
            ->all();
    }

    public function test_quiz_page_lists_all_questions()
    {
        $this->get('/test')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('quiz/show')
            ->has('questions', QuizQuestion::count())
        );
    }

    public function test_guest_can_complete_the_quiz_and_see_ranked_careers()
    {
        $response = $this->post('/test', ['answers' => $this->firstOptionAnswers()]);

        $result = QuizResult::sole();
        $response->assertRedirect(route('quiz.result', $result));
        $this->assertNull($result->user_id);

        $this->get(route('quiz.result', $result))->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('quiz/result')
            ->has('careers.0.score')
        );

        $scores = array_column($this->get(route('quiz.result', $result))->viewData('page')['props']['careers'], 'score');
        $sorted = $scores;
        rsort($sorted);
        $this->assertSame($sorted, $scores);
        $this->assertLessThanOrEqual(100, $scores[0]);
    }

    public function test_incomplete_answers_are_rejected()
    {
        $answers = $this->firstOptionAnswers();
        array_pop($answers);

        $this->post('/test', ['answers' => $answers])->assertSessionHasErrors('answers');
        $this->assertSame(0, QuizResult::count());
    }

    public function test_options_from_another_question_are_rejected()
    {
        $answers = $this->firstOptionAnswers();
        $questionIds = array_keys($answers);
        $answers[$questionIds[0]] = $answers[$questionIds[1]];

        $this->post('/test', ['answers' => $answers])->assertSessionHasErrors('answers');
    }

    public function test_guest_result_is_claimed_after_registration()
    {
        $this->post('/test', ['answers' => $this->firstOptionAnswers()]);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->whereNot('latestResult', null)
            ->has('topCareers', 3)
        );

        $this->assertSame($user->id, QuizResult::sole()->user_id);
    }
}
