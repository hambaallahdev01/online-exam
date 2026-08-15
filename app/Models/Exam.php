<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'question_group_id',
        'title',
        'token',
        'starts_at',
        'ends_at',
        'duration_minutes',
        'is_active',
        'randomize_questions',
        'randomize_options',
        'show_explanation_after_submit',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'show_explanation_after_submit' => 'boolean',
    ];

    public function scopeAvailableAt(Builder $query, CarbonInterface $instant): Builder
    {
        $utcInstant = CarbonImmutable::instance($instant)->utc();

        return $query
            ->where('is_active', true)
            ->where('starts_at', '<=', $utcInstant)
            ->where('ends_at', '>=', $utcInstant);
    }

    public function isAvailableAt(CarbonInterface $instant): bool
    {
        $utcInstant = CarbonImmutable::instance($instant)->utc();

        return $this->is_active
            && $this->starts_at->lte($utcInstant)
            && $this->ends_at->gte($utcInstant);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function questionGroup(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}
