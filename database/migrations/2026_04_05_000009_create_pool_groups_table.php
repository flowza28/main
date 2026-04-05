<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('start_ip');
            $table->string('end_ip');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('pool_group_id')->nullable()->constrained('pool_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pool_group_id');
        });

        Schema::dropIfExists('pool_groups');
    }
};
