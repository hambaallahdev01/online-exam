<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('question_group_id')->constrained('question_groups')->onDelete('cascade');
            $table->string('title');
            $table->string('token', 10)->index();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('duration_minutes');
            $table->boolean('is_active')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('show_explanation_after_submit')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
