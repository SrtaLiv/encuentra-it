<?php

namespace App\Models;

use App\Enums\AiMode;
use App\Enums\Level;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Challenge extends Model
{
    protected $fillable = [
        'career_id',
        'level',
        'ai_mode',
        'position',
        'title',
        'description',
        'deliverable',
        'guide',
        'solution',
    ];

    // the solution is only revealed after completing, see CareerController
    protected $hidden = [
        'solution',
    ];

    protected function casts(): array
    {
        return [
            'level' => Level::class,
            'ai_mode' => AiMode::class,
            'guide' => 'array',
        ];
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function completedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ChallengeCompletion::class)
            ->withPivot('evidence_url', 'reflection')
            ->withTimestamps();
    }
}
