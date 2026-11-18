<?php

namespace App\Services;

use App\Models\QuizQuestion;
use Illuminate\Support\Collection;

/**
 * scores each career as the percentage of the maximum points it could earn,
 * so careers mapped to many options don't win just by appearing more often.
 */
class QuizScorer
{
    /**
     * @param  array<int, int>  $optionIds
     * @return array<int, int> career id => match percentage, highest first
     */
    public function score(array $optionIds): array
    {
        $questions = $this->questions();
        $maxPoints = [];
        $earned = [];

        foreach ($questions as $question) {
            $bestPerCareer = [];

            foreach ($question->options as $option) {
                foreach ($option->careers as $career) {
                    $weight = (int) $career->pivot->weight;
                    $bestPerCareer[$career->id] = max($bestPerCareer[$career->id] ?? 0, $weight);

                    if (in_array($option->id, $optionIds, true)) {
                        $earned[$career->id] = ($earned[$career->id] ?? 0) + $weight;
                    }
                }
            }

            foreach ($bestPerCareer as $careerId => $weight) {
                $maxPoints[$careerId] = ($maxPoints[$careerId] ?? 0) + $weight;
            }
        }

        $scores = [];

        foreach ($maxPoints as $careerId => $max) {
            $scores[$careerId] = (int) round(($earned[$careerId] ?? 0) / $max * 100);
        }

        arsort($scores);

        return $scores;
    }

    /**
     * @return Collection<int, QuizQuestion>
     */
    public function questions(): Collection
    {
        return QuizQuestion::query()
            ->with('options.careers:id')
            ->orderBy('position')
            ->get();
    }
}
