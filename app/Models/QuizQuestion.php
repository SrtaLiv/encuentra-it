<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'text',
        'position',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('position');
    }
}
