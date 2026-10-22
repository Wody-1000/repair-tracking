<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('part_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // price at the time of use
            $table->timestamps();
        });

        DB::statement('ALTER TABLE ticket_parts ADD CONSTRAINT ticket_parts_quantity_check CHECK (quantity > 0)');
        DB::statement('ALTER TABLE ticket_parts ADD CONSTRAINT ticket_parts_unit_price_check CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_parts');
    }
};
