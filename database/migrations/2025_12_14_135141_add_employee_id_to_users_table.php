<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Add the username column only if it doesn't exist
        if (!Schema::hasColumn('users', 'username')) {
            $table->string('username')->unique();
        }

        // Add the position column only if it doesn't exist
        if (!Schema::hasColumn('users', 'position')) {
            $table->string('position');
        }

        // Add employee_id and make it the primary key
        if (!Schema::hasColumn('users', 'employee_id')) {
            $table->string('employee_id')->primary();
        }

        // Optionally, you can remove the 'id' column if you want
        // $table->dropColumn('id'); // Uncomment this if you want to drop 'id'
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['username', 'position']);
        $table->dropColumn('employee_id');
    });
}


};
