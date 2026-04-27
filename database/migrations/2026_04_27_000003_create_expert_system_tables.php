<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mental_disorders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->text('description')->nullable();
            $table->text('solution')->nullable();
            $table->timestamps();
        });

        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->decimal('belief', 5, 4);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('knowledge_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 20)->index();
            $table->foreignId('mental_disorder_id')->constrained('mental_disorders')->cascadeOnDelete();
            $table->foreignId('symptom_id')->constrained('symptoms')->cascadeOnDelete();
            $table->decimal('belief', 5, 4);
            $table->timestamps();

            $table->unique(['mental_disorder_id', 'symptom_id']);
        });

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('respondent_name')->nullable();
            $table->unsignedTinyInteger('respondent_age')->nullable();
            $table->string('respondent_gender', 20)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('detected_mental_disorder_id')->nullable()->constrained('mental_disorders')->nullOnDelete();
            $table->decimal('confidence_score', 6, 5)->nullable();
            $table->decimal('confidence_percentage', 5, 2)->nullable();
            $table->string('certainty_label')->nullable();
            $table->json('mass_values')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('consultation_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('symptom_id')->constrained()->cascadeOnDelete();
            $table->decimal('belief', 5, 4);
            $table->boolean('selected')->default(true);
            $table->unsignedSmallInteger('sort_order')->nullable();
            $table->timestamps();

            $table->unique(['consultation_id', 'symptom_id']);
        });

        Schema::create('consultation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mental_disorder_id')->constrained('mental_disorders')->cascadeOnDelete();
            $table->decimal('belief', 6, 5);
            $table->decimal('percentage', 5, 2);
            $table->unsignedSmallInteger('rank')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->timestamps();

            $table->unique(['consultation_id', 'mental_disorder_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_results');
        Schema::dropIfExists('consultation_symptoms');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('knowledge_rules');
        Schema::dropIfExists('symptoms');
        Schema::dropIfExists('mental_disorders');
    }
};
