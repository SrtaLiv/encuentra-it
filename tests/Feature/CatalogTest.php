<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_load()
    {
        $this->get('/')->assertOk();
        $this->get('/carreras')->assertOk();
        $this->get('/recursos')->assertOk();
        $this->get('/carreras/devops-engineer')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('careers/show')
            ->where('career.slug', 'devops-engineer')
            ->has('career.steps')
            ->has('career.resources')
        );
    }

    public function test_careers_can_be_filtered_by_coding_level()
    {
        $this->get('/carreras?coding=none')->assertInertia(fn (Assert $page) => $page
            ->where('careers', fn ($careers) => collect($careers)->every(fn ($career) => $career['coding_level'] === 'none'))
        );
    }

    public function test_invalid_filters_do_not_crash()
    {
        $this->get('/carreras?coding=hacker')->assertRedirect();
        $this->get('/recursos?type=nope&language=fr')->assertRedirect();
    }

    public function test_unpublished_career_is_not_visible()
    {
        Career::where('slug', 'pentester')->update(['is_published' => false]);

        $this->get('/carreras/pentester')->assertNotFound();
    }

    public function test_user_can_toggle_roadmap_progress()
    {
        $user = User::factory()->create();
        $step = Career::where('slug', 'qa-manual')->first()->steps()->first();

        $this->actingAs($user)->post(route('steps.toggle', $step))->assertRedirect();
        $this->assertTrue($user->completedSteps()->whereKey($step->id)->exists());

        $this->actingAs($user)->post(route('steps.toggle', $step));
        $this->assertFalse($user->completedSteps()->whereKey($step->id)->exists());
    }

    public function test_guest_cannot_toggle_progress()
    {
        $step = Career::first()->steps()->first();

        $this->post(route('steps.toggle', $step))->assertRedirect('/login');
    }
}
