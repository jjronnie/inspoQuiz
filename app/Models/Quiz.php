<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'time_limit_minutes',
        'is_published',
        'user_id',
    ];

    /**
     * The quiz is created by a user (admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A quiz has many questions.
     */
    public function questions(): HasMany
    {
        // Order by the 'order' column for the user attempt flow
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
