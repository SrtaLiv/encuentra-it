<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuizOption extends Model
{
    protected $fillable = [
        'quiz_question_id',
        'text',
        'position',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class)->withPivot('weight');
    }
}
