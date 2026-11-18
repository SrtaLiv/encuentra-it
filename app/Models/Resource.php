<?php

namespace App\Models;

use App\Enums\Level;
use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Resource extends Model
{
    protected $fillable = [
        'title',
        'url',
        'description',
        'type',
        'level',
        'language',
        'is_free',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'type' => ResourceType::class,
            'level' => Level::class,
            'is_free' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class);
    }
}
