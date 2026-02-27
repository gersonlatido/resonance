<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'table_label')) {
                $table->string('table_label')->nullable()->after('table_number');
            }
            if (!Schema::hasColumn('orders', 'table_numbers')) {
                $table->json('table_numbers')->nullable()->after('table_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'table_numbers')) $table->dropColumn('table_numbers');
            if (Schema::hasColumn('orders', 'table_label')) $table->dropColumn('table_label');
        });
    }
};