<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_no', 30)->unique();  // e.g. INS-20240101-001
            $table->foreignId('receiving_id')
                  ->constrained('receivings')
                  ->cascadeOnDelete();
            $table->enum('status', ['OPEN', 'WAITING_APPROVAL', 'CLOSED'])
                  ->default('OPEN');

            // Inspector info
            $table->foreignId('inspector_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('inspected_at')->nullable();

            // Approval info
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Rejection info
            $table->foreignId('rejected_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->text('notes')->nullable();
            $table->text('vin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
