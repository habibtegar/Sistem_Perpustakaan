<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'stock')) {
                $table->unsignedInteger('stock')->default(5)->after('description');
            }
            if (!Schema::hasColumn('books', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('books', 'stock')) {
                $table->dropColumn('stock');
            }
        });
    }
};
