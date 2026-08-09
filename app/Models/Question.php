<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'question_group_id',
        'question_type',
        'content',
        'options_json',
        'correct_answers_json',
        'explanation',
        'weight',
    ];

    protected $casts = [
        'options_json' => 'array',
        'correct_answers_json' => 'array',
        'weight' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Question $question) {
            \App\Services\MediaUploadService::deleteMediaFromContent($question->content);
            \App\Services\MediaUploadService::deleteMediaFromContent($question->options_json);
            \App\Services\MediaUploadService::deleteMediaFromContent($question->explanation);
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function questionGroup(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class);
    }
}
