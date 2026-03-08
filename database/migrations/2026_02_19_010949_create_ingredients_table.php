<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();

            $table->enum('unit', ['g','ml','pcs']);

            // current stock
            $table->decimal('stock_qty', 12, 2)->default(0);

            // minimum stock before warning
            $table->decimal('reorder_level', 12, 2)->default(0);

            // ✅ NEW: maximum recommended stock before overstock warning
            $table->decimal('overstock_level', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};