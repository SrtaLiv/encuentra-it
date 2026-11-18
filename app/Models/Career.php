<?php

namespace App\Models;

use App\Enums\CodingLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'slug',
        'summary',
        'description',
        'coding_level',
        'daily_tasks',
        'skills',
        'job_titles',
        'job_outlook',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'coding_level' => CodingLevel::class,
            'daily_tasks' => 'array',
            'skills' => 'array',
            'job_titles' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RoadmapStep::class)->orderBy('position');
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class)->orderBy('position');
    }

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class);
    }
}
