<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChallengeCompletion extends Pivot
{
    protected $table = 'challenge_user';

    protected function casts(): array
    {
        return [
            'reflection' => 'array',
        ];
    }
}
