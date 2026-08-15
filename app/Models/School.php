<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'address',
        'phone',
        'locale',
        'timezone',
    ];

    protected static function booted(): void
    {
        static::creating(function (School $school): void {
            $school->timezone ??= config('tenancy.default_timezone', 'Asia/Jakarta');
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function questionGroups(): HasMany
    {
        return $this->hasMany(QuestionGroup::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
