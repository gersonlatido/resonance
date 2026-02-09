<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // from cart
            $table->string('menu_id'); // your menu_id is string-ish in JS, keep it as string
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('qty');
            $table->string('image')->nullable();

            $table->decimal('line_total', 10, 2); // price * qty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
