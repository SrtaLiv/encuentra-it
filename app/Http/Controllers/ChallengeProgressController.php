<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChallengeProgressController extends Controller
{
    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        // explaining what you did is what turns a finished task into learning
        $validated = $request->validate([
            'evidence_url' => ['nullable', 'url:http,https', 'max:500'],
            'reflection.decision' => ['required', 'string', 'min:20', 'max:1000'],
            'reflection.difficulty' => ['required', 'string', 'min:20', 'max:1000'],
            'reflection.next' => ['nullable', 'string', 'max:1000'],
        ], attributes: [
            'reflection.decision' => 'qué decidiste y por qué',
            'reflection.difficulty' => 'qué te costó',
            'reflection.next' => 'qué harías distinto',
        ]);

        $request->user()->completedChallenges()->syncWithoutDetaching([
            $challenge->id => [
                'evidence_url' => $validated['evidence_url'] ?? null,
                'reflection' => [
                    'decision' => trim($validated['reflection']['decision']),
                    'difficulty' => trim($validated['reflection']['difficulty']),
                    'next' => trim($validated['reflection']['next'] ?? '') ?: null,
                ],
            ],
        ]);

        return back();
    }

    public function destroy(Request $request, Challenge $challenge): RedirectResponse
    {
        $request->user()->completedChallenges()->detach($challenge->id);

        return back();
    }
}
