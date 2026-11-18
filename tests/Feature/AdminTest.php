<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Career;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->forceFill(['is_admin' => true])->save();
    }

    public function test_non_admins_cannot_access_admin()
    {
        $this->get('/admin/careers')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin/careers')->assertForbidden();
    }

    public function test_admin_pages_load()
    {
        $career = Career::first();

        foreach (['/admin/areas', '/admin/careers', '/admin/resources', '/admin/quiz', '/admin/careers/create', "/admin/careers/{$career->slug}/edit"] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_updating_career_keeps_existing_step_ids_and_user_progress()
    {
        $career = Career::where('slug', 'qa-manual')->with('steps')->first();
        $kept = $career->steps->first();
        $user = User::factory()->create();
        $user->completedSteps()->attach($kept->id);

        $this->actingAs($this->admin)->put(route('admin.careers.update', $career), [
            'area_id' => $career->area_id,
            'name' => $career->name,
            'slug' => '',
            'summary' => $career->summary,
            'description' => $career->description,
            'coding_level' => 'none',
            'daily_tasks' => ['  Probar cosas  ', ''],
            'skills' => ['Jira'],
            'job_titles' => ['QA'],
            'is_published' => true,
            'steps' => [
                ['id' => null, 'level' => 'beginner', 'title' => 'Paso nuevo', 'description' => ''],
                ['id' => $kept->id, 'level' => 'beginner', 'title' => 'Renombrado', 'description' => null],
            ],
        ])->assertRedirect(route('admin.careers.index'));

        $career->refresh();
        $this->assertSame(['Paso nuevo', 'Renombrado'], $career->steps()->pluck('title')->all());
        $this->assertSame(['Probar cosas'], $career->daily_tasks);
        $this->assertSame('qa-manual-tester', $career->slug);
        $this->assertTrue($user->completedSteps()->whereKey($kept->id)->exists());
    }

    public function test_area_with_careers_cannot_be_deleted()
    {
        $area = Area::has('careers')->first();

        $this->actingAs($this->admin)->delete(route('admin.areas.destroy', $area))->assertSessionHas('error');
        $this->assertModelExists($area);
    }

    public function test_resource_url_must_be_http()
    {
        $this->actingAs($this->admin)->post(route('admin.resources.store'), [
            'title' => 'Malo',
            'url' => 'javascript:alert(1)',
            'type' => 'course',
            'level' => 'beginner',
            'language' => 'es',
        ])->assertSessionHasErrors('url');
    }

    public function test_make_admin_command()
    {
        $user = User::factory()->create();

        $this->artisan('app:make-admin', ['email' => $user->email])->assertExitCode(0);
        $this->assertTrue($user->fresh()->is_admin);

        $this->artisan('app:make-admin', ['email' => 'nadie@example.com'])->assertExitCode(1);
    }
}
