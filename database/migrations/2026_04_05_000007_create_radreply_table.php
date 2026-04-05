<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radreply', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('attribute');
            $table->string('op')->default(':=');
            $table->string('value');
            $table->timestamps();
            $table->index('username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radreply');
    }
};
