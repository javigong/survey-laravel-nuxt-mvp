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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade')->index();
            $table->string('title'); // The question text
            $table->text('description')->nullable(); // Optional question description
            $table->enum('type', [
                'multiple_choice_single',
                'multiple_choice_multiple', 
                'text_short',
                'text_long',
                'rating_scale',
                'yes_no',
                'dropdown',
                'checkbox',
                'date',
                'time',
                'datetime',
                'file_upload'
            ]);
            $table->json('options')->nullable(); // For multiple choice, dropdown, checkbox options
            $table->json('validation_rules')->nullable(); // Custom validation rules
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0); // Question order within survey
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['survey_id', 'order']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
