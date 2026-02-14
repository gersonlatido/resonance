<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();

            // link to Xendit invoice
            $table->string('external_id')->unique();

            $table->unsignedInteger('table_number')->index();

            // preparing/serving/served/cancelled
            $table->enum('status', ['preparing','serving','served','cancelled'])->default('preparing')->index();

            // ETA minutes shown to customer
            $table->unsignedInteger('eta_minutes')->nullable();

            // payment state
            $table->enum('payment_status', ['unpaid','paid','failed'])->default('unpaid')->index();

            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
