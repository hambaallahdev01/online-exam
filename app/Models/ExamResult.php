<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'exam_id',
        'student_id',
        'answers_json',
        'score',
        'time_remaining_seconds',
        'status',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'answers_json' => 'array',
        'score' => 'decimal:2',
        'time_remaining_seconds' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
