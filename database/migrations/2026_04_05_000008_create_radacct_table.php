<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radacct', function (Blueprint $table) {
            $table->id();
            $table->string('acctsessionid')->nullable();
            $table->string('acctuniqueid')->nullable();
            $table->string('username')->nullable();
            $table->string('realm')->nullable();
            $table->string('nasipaddress')->nullable();
            $table->string('nasportid')->nullable();
            $table->string('nasporttype')->nullable();
            $table->dateTime('acctstarttime')->nullable();
            $table->dateTime('acctstoptime')->nullable();
            $table->integer('acctsessiontime')->nullable();
            $table->string('acctauthentic')->nullable();
            $table->string('connectinfo_start')->nullable();
            $table->string('connectinfo_stop')->nullable();
            $table->decimal('acctinputoctets', 20, 0)->default(0);
            $table->decimal('acctoutputoctets', 20, 0)->default(0);
            $table->string('acctterminatecause')->nullable();
            $table->string('service_type')->nullable();
            $table->string('framedprotocol')->nullable();
            $table->string('framedipaddress')->nullable();
            $table->timestamps();
            $table->index('username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radacct');
    }
};
