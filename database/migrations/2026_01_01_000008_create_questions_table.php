<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('question_group_id')->constrained('question_groups')->onDelete('cascade');
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false', 'essay', 'fact_opinion', 'matching', 'sorting'])->default('single_choice');
            $table->text('content');
            $table->json('options_json')->nullable()->comment('Array of options, e.g. [{id: "A", text: "..."}, ...]');
            $table->json('correct_answers_json')->nullable()->comment('Array or string of correct answer keys/texts');
            $table->text('explanation')->nullable()->comment('Pembahasan / Answer explanation');
            $table->integer('weight')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
