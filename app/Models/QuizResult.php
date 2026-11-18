<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QuizResult extends Model
{
    protected $fillable = [
        'user_id',
        'answers',
        'scores',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'scores' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (QuizResult $result) {
            $result->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
