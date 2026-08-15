<?php

namespace App\Models;

use App\Services\ExamQuestionPayload;
use App\Services\MediaUploadService;
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
        static::saved(function (Question $question) {
            app(ExamQuestionPayload::class)->forgetQuestionGroup($question->question_group_id);
        });

        static::deleting(function (Question $question) {
            $teacherId = $question->questionGroup()->value('teacher_id');
            $requiredPrefix = $teacherId ? "questions/{$question->school_id}/{$teacherId}" : '__invalid_media_owner__';

            MediaUploadService::deleteMediaFromContent($question->content, $requiredPrefix);
            MediaUploadService::deleteMediaFromContent($question->options_json, $requiredPrefix);
            MediaUploadService::deleteMediaFromContent($question->explanation, $requiredPrefix);
        });

        static::deleted(function (Question $question) {
            app(ExamQuestionPayload::class)->forgetQuestionGroup($question->question_group_id);
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
