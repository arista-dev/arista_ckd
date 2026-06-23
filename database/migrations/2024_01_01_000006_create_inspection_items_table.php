<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                  ->constrained('inspections')
                  ->cascadeOnDelete();
            $table->foreignId('component_id')
                  ->constrained('components');

            // Snapshot at time of inspection (component data may change)
            $table->string('component_code', 20);
            $table->string('component_name', 100);
            $table->unsignedInteger('expected_qty');

            // Inspector input
            $table->unsignedInteger('actual_qty')->nullable();
            $table->unsignedInteger('short_qty')->default(0);
            $table->enum('status', ['OK', 'SHORT', 'DAMAGE'])->default('OK');

            // Damage fields
            $table->text('damage_remark')->nullable();
            $table->string('damage_photo', 255)->nullable();  // filename in storage

            $table->timestamps();

            $table->unique(['inspection_id', 'component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
