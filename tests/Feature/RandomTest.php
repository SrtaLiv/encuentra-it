<?php

namespace Tests\Feature;

use App\Models\Career;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RandomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_surprise_mode_returns_some_item()
    {
        $this->get('/azar')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('random')
            ->where('mode', null)
            ->where('type', fn ($type) => in_array($type, ['carrera', 'recurso', 'desafio']))
            ->whereNot('item', null)
        );
    }

    public function test_each_type_returns_its_own_item()
    {
        $this->get('/azar?tipo=carrera')->assertInertia(fn (Assert $page) => $page
            ->where('type', 'carrera')
            ->has('item.slug')
            ->has('item.area')
        );

        $this->get('/azar?tipo=recurso')->assertInertia(fn (Assert $page) => $page
            ->where('type', 'recurso')
            ->has('item.url')
            ->has('item.careers')
        );

        $this->get('/azar?tipo=desafio')->assertInertia(fn (Assert $page) => $page
            ->where('type', 'desafio')
            ->has('item.deliverable')
            ->has('item.career.slug')
        );
    }

    public function test_previous_item_is_skipped()
    {
        $career = Career::query()->published()->first();

        foreach (range(1, 10) as $attempt) {
            $this->get("/azar?tipo=carrera&sin={$career->id}")->assertInertia(fn (Assert $page) => $page
                ->where('item.id', fn ($id) => $id !== $career->id)
            );
        }
    }

    public function test_only_published_careers_are_picked()
    {
        $visible = Career::query()->published()->first();
        Career::whereKeyNot($visible->id)->update(['is_published' => false]);

        $this->get('/azar?tipo=carrera')->assertInertia(fn (Assert $page) => $page->where('item.id', $visible->id));
        $this->get('/azar?tipo=desafio')->assertInertia(fn (Assert $page) => $page->where('item.career.id', $visible->id));
    }

    public function test_invalid_type_does_not_crash()
    {
        $this->get('/azar?tipo=nope')->assertRedirect();
        $this->get('/azar?tipo=carrera&sin=abc')->assertRedirect();
    }
}
