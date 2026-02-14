<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_code')) {
                $table->string('order_code')->unique()->after('id');
            }
            if (!Schema::hasColumn('orders', 'external_id')) {
                $table->string('external_id')->unique()->after('order_code');
            }
            if (!Schema::hasColumn('orders', 'table_number')) {
                $table->unsignedInteger('table_number')->after('external_id');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['preparing','serving','served','cancelled'])->default('preparing')->after('table_number');
            }
            if (!Schema::hasColumn('orders', 'eta_minutes')) {
                $table->unsignedInteger('eta_minutes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['unpaid','paid','failed'])->default('unpaid')->after('eta_minutes');
            }
            if (!Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 10, 2)->default(0)->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // optional: drop columns if you want rollback
        });
    }
};
