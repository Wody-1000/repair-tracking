<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sequence used to build ticket numbers like RT-2026-0001 (race-free, no duplicates)
        DB::statement('DROP SEQUENCE IF EXISTS ticket_number_seq');
        DB::statement('CREATE SEQUENCE ticket_number_seq START 1');

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('device_type', 50);
            $table->string('brand', 100);
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->text('problem_description');
            $table->foreignId('status_id')->constrained('repair_statuses')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_labor_cost_check CHECK (labor_cost >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        DB::statement('DROP SEQUENCE IF EXISTS ticket_number_seq');
    }
};
