<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_no', 30)->unique();   // e.g. RCV-20240101-001
            $table->string('container_no', 50);
            $table->foreignId('ckd_model_id')
                  ->constrained('ckd_models');
            $table->date('receive_date');
            $table->enum('status', ['RECEIVED', 'INSPECTION_OPEN', 'CLOSED'])
                  ->default('INSPECTION_OPEN');
            $table->foreignId('created_by')
                  ->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
