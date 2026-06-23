<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ckd_model_id')
                  ->constrained('ckd_models')
                  ->cascadeOnDelete();
            $table->string('code', 20);             // e.g. BP, MA, WHL
            $table->string('name', 100);
            $table->unsignedInteger('expected_qty')->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['ckd_model_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
