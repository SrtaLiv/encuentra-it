<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * updates children by id instead of recreating them, so ids referenced
 * elsewhere (user progress, quiz answers) survive an edit.
 */
trait SyncsChildren
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  callable(array<string, mixed>, int): array<string, mixed>  $attributes
     * @return array<int, Model> saved children in the same order as $items
     */
    protected function syncChildren(HasMany $relation, array $items, callable $attributes): array
    {
        $existing = $relation->get()->keyBy('id');
        $saved = [];

        foreach (array_values($items) as $index => $item) {
            $data = $attributes($item, $index);
            $child = $existing->get($item['id'] ?? null);

            if ($child) {
                $child->update($data);
            } else {
                $child = $relation->create($data);
            }

            $saved[] = $child;
        }

        $existing->except(array_map(fn (Model $child) => $child->id, $saved))->each->delete();

        return $saved;
    }
}
