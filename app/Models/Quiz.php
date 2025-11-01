<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'time_limit',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getQuestionsCountAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getAttemptsCountAttribute(): int
    {
        return $this->attempts()->count();
    }
}