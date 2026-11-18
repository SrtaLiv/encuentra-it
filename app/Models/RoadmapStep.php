<?php

namespace App\Models;

use App\Enums\Level;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoadmapStep extends Model
{
    protected $fillable = [
        'career_id',
        'level',
        'position',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'level' => Level::class,
        ];
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function completedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
