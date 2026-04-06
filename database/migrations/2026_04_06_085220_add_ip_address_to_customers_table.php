<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('address');
            $table->dropForeign(['pool_group_id']);
            $table->dropColumn('pool_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('pool_group_id')->nullable()->constrained('pool_groups')->cascadeOnDelete();
            $table->dropColumn('ip_address');
        });
    }
};
