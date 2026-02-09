<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // If unit_price exists but has no default, set a default.
        // If it doesn't exist, create it.
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0)->after('name');
            }
        });

        // Force default value at DB-level (works even if column existed already)
        DB::statement("ALTER TABLE order_items MODIFY unit_price DECIMAL(10,2) NOT NULL DEFAULT 0");

        // Make sure qty exists
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'qty')) {
                $table->integer('qty')->default(1)->after('unit_price');
            }
            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->decimal('line_total', 10, 2)->default(0)->after('qty');
            }
            if (!Schema::hasColumn('order_items', 'image')) {
                $table->string('image')->nullable()->after('line_total');
            }
            if (!Schema::hasColumn('order_items', 'menu_id')) {
                $table->string('menu_id')->nullable()->after('order_id');
            }
        });
    }

    public function down(): void
    {
        // we won't drop unit_price to avoid breaking old data
    }
};
