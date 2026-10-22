<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->timestamps();

            $table->unique(['name', 'phone']); // same person is reused, not duplicated
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
