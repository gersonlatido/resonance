<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            // Add columns only if they don't exist (prevents errors)
            if (!Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('name');
            }

            if (!Schema::hasColumn('order_items', 'qty')) {
                $table->integer('qty')->default(1)->after('price');
            }

            if (!Schema::hasColumn('order_items', 'image')) {
                $table->string('image')->nullable()->after('qty');
            }

            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->decimal('line_total', 10, 2)->default(0)->after('image');
            }

            // If your table doesn't have menu_id, add it too:
            if (!Schema::hasColumn('order_items', 'menu_id')) {
                $table->string('menu_id')->nullable()->after('order_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'line_total')) $table->dropColumn('line_total');
            if (Schema::hasColumn('order_items', 'image')) $table->dropColumn('image');
            if (Schema::hasColumn('order_items', 'qty')) $table->dropColumn('qty');
            if (Schema::hasColumn('order_items', 'price')) $table->dropColumn('price');
            if (Schema::hasColumn('order_items', 'menu_id')) $table->dropColumn('menu_id');
        });
    }
};
