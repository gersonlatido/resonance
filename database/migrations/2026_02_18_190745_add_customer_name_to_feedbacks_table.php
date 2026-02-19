<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            // add only if not existing yet
            if (!Schema::hasColumn('feedbacks', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('feedbacks', 'table_number')) {
                $table->integer('table_number')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('feedbacks', 'rating')) {
                $table->integer('rating')->default(0)->after('table_number');
            }
            if (!Schema::hasColumn('feedbacks', 'comment')) {
                $table->text('comment')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('feedbacks', 'is_reviewed')) {
                $table->boolean('is_reviewed')->default(0)->after('comment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            if (Schema::hasColumn('feedbacks', 'customer_name')) $table->dropColumn('customer_name');
            if (Schema::hasColumn('feedbacks', 'table_number')) $table->dropColumn('table_number');
            if (Schema::hasColumn('feedbacks', 'rating')) $table->dropColumn('rating');
            if (Schema::hasColumn('feedbacks', 'comment')) $table->dropColumn('comment');
            if (Schema::hasColumn('feedbacks', 'is_reviewed')) $table->dropColumn('is_reviewed');
        });
    }
};
