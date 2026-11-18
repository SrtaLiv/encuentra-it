<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Challenge;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ChallengeTest extends TestCase
{
    use RefreshDatabase;

    private Career $career;

    private const REFLECTION = [
        'decision' => 'Elegí una cuenta separada para mi nicho de QA.',
        'difficulty' => 'Me costó escribir una bio corta y clara.',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->career = Career::where('slug', 'creador-de-contenido')->firstOrFail();
    }

    public function test_every_career_has_challenges()
    {
        $this->assertSame(0, Career::doesntHave('challenges')->count());
    }

    public function test_career_page_shows_challenges_in_order()
    {
        $this->get(route('careers.show', $this->career))->assertInertia(fn (Assert $page) => $page
            ->where('career.challenges.0.title', 'Creá tu cuenta profesional')
            ->has('career.challenges.0.deliverable')
            ->where('completedChallenges', [])
        );
    }

    public function test_user_can_complete_a_challenge_with_evidence_link()
    {
        $user = User::factory()->create();
        $challenge = $this->career->challenges()->first();

        $this->actingAs($user)
            ->post(route('challenges.complete', $challenge), ['evidence_url' => 'https://www.tiktok.com/@yo/video/1', 'reflection' => self::REFLECTION])
            ->assertRedirect();

        $this->actingAs($user)->get(route('careers.show', $this->career))->assertInertia(fn (Assert $page) => $page
            ->where("completedChallenges.{$challenge->id}.evidence_url", 'https://www.tiktok.com/@yo/video/1')
            ->where("completedChallenges.{$challenge->id}.reflection.decision", self::REFLECTION['decision'])
        );

        $this->actingAs($user)->get('/dashboard')->assertInertia(fn (Assert $page) => $page
            ->has('portfolio', 1)
            ->where('portfolio.0.title', $challenge->title)
            ->where('portfolio.0.career.slug', 'creador-de-contenido')
        );
    }

    public function test_evidence_link_is_optional_but_must_be_http()
    {
        $user = User::factory()->create();
        $challenge = Challenge::first();

        $this->actingAs($user)->post(route('challenges.complete', $challenge), ['evidence_url' => 'javascript:alert(1)', 'reflection' => self::REFLECTION])
            ->assertSessionHasErrors('evidence_url');
        $this->assertFalse($user->completedChallenges()->exists());

        $this->actingAs($user)->post(route('challenges.complete', $challenge), ['reflection' => self::REFLECTION])->assertSessionHasNoErrors();
        $this->assertTrue($user->completedChallenges()->whereKey($challenge->id)->exists());
    }

    public function test_user_can_undo_a_challenge()
    {
        $user = User::factory()->create();
        $challenge = Challenge::first();
        $user->completedChallenges()->attach($challenge->id);

        $this->actingAs($user)->delete(route('challenges.undo', $challenge))->assertRedirect();
        $this->assertFalse($user->completedChallenges()->exists());
    }

    public function test_guest_cannot_complete_challenges()
    {
        $this->post(route('challenges.complete', Challenge::first()))->assertRedirect('/login');
    }

    public function test_admin_edit_keeps_challenge_ids_and_progress()
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $user = User::factory()->create();
        $kept = $this->career->challenges()->first();
        $user->completedChallenges()->attach($kept->id);

        $this->actingAs($admin)->put(route('admin.careers.update', $this->career), [
            ...$this->career->only(['area_id', 'name', 'slug', 'summary', 'description', 'daily_tasks', 'skills', 'job_titles', 'job_outlook']),
            'coding_level' => 'none',
            'is_published' => true,
            'challenges' => [
                ['id' => $kept->id, 'level' => 'beginner', 'title' => 'Cuenta creada', 'description' => 'Hacelo.', 'deliverable' => 'Link', 'ai_mode' => 'none'],
                ['id' => null, 'level' => 'advanced', 'title' => 'Nuevo', 'description' => 'Hacelo.', 'deliverable' => 'Link', 'ai_mode' => 'allowed', 'solution' => 'Así se hace.'],
            ],
        ])->assertRedirect(route('admin.careers.index'));

        $this->assertSame(['Cuenta creada', 'Nuevo'], $this->career->challenges()->pluck('title')->all());
        $this->assertTrue($user->completedChallenges()->whereKey($kept->id)->exists());
        $this->assertGreaterThan(0, $this->career->steps()->count(), 'steps not sent must not be wiped');
    }

    public function test_completing_requires_a_real_explanation()
    {
        $user = User::factory()->create();
        $challenge = Challenge::first();

        $this->actingAs($user)->post(route('challenges.complete', $challenge), ['reflection' => ['decision' => 'ok', 'difficulty' => '']])
            ->assertSessionHasErrors(['reflection.decision', 'reflection.difficulty']);

        $this->assertFalse($user->completedChallenges()->exists());
    }

    public function test_every_career_has_an_ai_audit_without_ai()
    {
        $this->assertSame(0, Career::whereDoesntHave('challenges', fn ($query) => $query
            ->where('title', 'Auditá a la IA')
            ->where('ai_mode', 'none')
            ->whereNotNull('solution'))->count());
    }

    public function test_solution_is_hidden_until_the_challenge_is_completed()
    {
        $user = User::factory()->create();
        $audit = $this->career->challenges()->where('title', 'Auditá a la IA')->firstOrFail();
        $index = $this->career->challenges()->pluck('id')->search($audit->id);

        $this->actingAs($user)->get(route('careers.show', $this->career))->assertInertia(fn (Assert $page) => $page
            ->where("career.challenges.{$index}.has_solution", true)
            ->missing("career.challenges.{$index}.solution")
        );

        $this->actingAs($user)->post(route('challenges.complete', $audit), ['reflection' => self::REFLECTION]);

        $this->actingAs($user)->get(route('careers.show', $this->career))->assertInertia(fn (Assert $page) => $page
            ->where("career.challenges.{$index}.solution", $audit->solution)
        );
    }

    public function test_every_beginner_challenge_has_a_guide()
    {
        $this->assertSame(0, Challenge::where('level', 'beginner')->whereNull('guide')->count());

        $this->get(route('careers.show', $this->career))->assertInertia(fn (Assert $page) => $page
            ->has('career.challenges.0.guide.steps')
            ->has('career.challenges.0.guide.hints')
        );
    }

    public function test_admin_guide_drops_blank_lines_and_empty_guides()
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        [$first, $second] = $this->career->challenges()->take(2)->get();

        $this->actingAs($admin)->put(route('admin.careers.update', $this->career), [
            ...$this->career->only(['area_id', 'name', 'slug', 'summary', 'description', 'daily_tasks', 'skills', 'job_titles', 'job_outlook']),
            'coding_level' => 'none',
            'is_published' => true,
            'challenges' => [
                ['id' => $first->id, 'level' => 'beginner', 'title' => 'A', 'description' => 'Hacelo.', 'deliverable' => 'Link', 'ai_mode' => 'tutor',
                    'guide' => ['before' => ['Tener cuenta', ''], 'steps' => ['  Paso uno  ', '', 'Paso dos'], 'hints' => []]],
                ['id' => $second->id, 'level' => 'beginner', 'title' => 'B', 'description' => 'Hacelo.', 'deliverable' => 'Link', 'ai_mode' => 'tutor',
                    'guide' => ['before' => [''], 'steps' => [], 'hints' => ['']]],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame(['before' => ['Tener cuenta'], 'steps' => ['Paso uno', 'Paso dos'], 'hints' => []], $first->fresh()->guide);
        $this->assertNull($second->fresh()->guide);
    }
}
