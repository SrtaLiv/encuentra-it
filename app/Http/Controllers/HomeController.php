<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Career;
use App\Models\Resource;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $areas = Area::query()
            ->withCount(['careers' => fn ($query) => $query->published()])
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'description', 'icon']);

        return Inertia::render('welcome', [
            'areas' => $areas,
            'stats' => [
                'careers' => Career::query()->published()->count(),
                'resources' => Resource::query()->published()->count(),
            ],
        ]);
    }
}
