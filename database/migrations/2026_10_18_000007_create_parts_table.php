<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 150);
            $table->foreignId('category_id')->constrained('part_categories')->restrictOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(5);
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE parts ADD CONSTRAINT parts_quantity_check CHECK (quantity >= 0)');
        DB::statement('ALTER TABLE parts ADD CONSTRAINT parts_reorder_level_check CHECK (reorder_level >= 0)');
        DB::statement('ALTER TABLE parts ADD CONSTRAINT parts_unit_price_check CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
