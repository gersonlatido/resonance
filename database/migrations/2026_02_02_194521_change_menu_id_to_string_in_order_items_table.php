<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // This uses raw SQL because changing column type needs DBAL sometimes.
        // Raw SQL works fine for MySQL.
        DB::statement("ALTER TABLE order_items MODIFY menu_id VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        // revert to INT if you really want (usually you won't)
        DB::statement("ALTER TABLE order_items MODIFY menu_id BIGINT NOT NULL");
    }
};
