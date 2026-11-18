<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'position',
    ];

    public function careers(): HasMany
    {
        return $this->hasMany(Career::class);
    }
}
