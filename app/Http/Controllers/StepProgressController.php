<?php

namespace App\Http\Controllers;

use App\Models\RoadmapStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StepProgressController extends Controller
{
    public function __invoke(Request $request, RoadmapStep $step): RedirectResponse
    {
        $request->user()->completedSteps()->toggle($step->id);

        return back();
    }
}
